
$(document).on( "click", "#boq-all-list", function(){

    /*$('.acco-two input[type=radio]').trigger('click');

    var id= $(this).attr('data-id');

    $('#boqprojectid').val(id);

    $('#itemofwork').val('none');

    $('#boqprojectname').html(getProjectname(id));

    $('#boqlistsection').show();*/

    $('#listboq').trigger('click') ;
    $('#cancel-import_boq').trigger('click') ;
    

    //$.ajax({

       // type: 'POST',

        //url: '../ProjectPricing/Wbssearch123',

        //dataType: "json",

        //data: {projectid:id},

        //success: function(data){

           // if(data.error=='No')

            //{

                //$('#wbs').html(data.result);

            //}

        //}

    //});

});
//  $(function(){
//      count =0;
//      $(document).on('click','.boq-all-list',function(){
        
//         count += 1;
//         if (count % 2 === 0) { 
//             $('.acco-two').slideUp();
//          }else {
//              if (count === 1) { 
//                  alert(count);
//              }else{
//                  $('.acco-two').slideDown(); alert(count);
//              }
//          }
//      });
//  });
$(function(){

    $(".custom-file-input").on("change", function() {
        
      var fileName = $(this).val().split("\\").pop();
      $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });

    $('#listboq').click(function(){ 

        $.ajax({ 

            type: 'POST',

            url: '../projectsmain/boqsearch',

            beforeSend : function(){

                $('#Promain-preloader-Listboq').show();

            },

            dataType: "json",

            data: {projectid:$('#boqprojectid').val()},

            success: function(data){

                if(data.error=='No')

                {
                    $('#boqprojectname').html(getProjectname(data.projectid));
                    $('#projectnameBoq').show();
                    $('#boqlistsection').show();
                    $('.content-action-wrpr').show();
                    $('#boq-list-body').html(data.result);
                    //$('#printbtn').html(data.print);
                    $('#importbtn').html(data.import);
                    $('#clearboqbtn').html(data.boqclear);
                    //$('#projectnameBoqnodata"').show();

                }

                $('#Promain-preloader-Listboq').hide();

            }

        });

    });
    $(document).on('click','#boqimport',function(){
        var projectid=$(this).attr('data-id');
        $(this).parents('.tab').addClass('add-form-active');
		$(this).parents('.tab').removeClass('edit-form-active');
        $('.custom-file-label').html('Choose file');
        $('#boq_projid').val(projectid);
    });
    $(document).on('click','#cancel-import_boq',function(){
        $(this).parents('.tab').removeClass('edit-form-active');
        $(this).parents('.tab').removeClass('add-form-active');
        $('#import_boq_form')[0].reset();
        $('#message').html('');
        $('#listboq').trigger('click') ;
    });

    $(document).on('click','#clearboq',function(){

        var projectid = $(this).attr('data-id');
    
        var r = confirm("Are you sure you want to clear all the boq items?");
    
        if (r == true) {
    
            $.ajax({
    
                    type: 'POST',
    
                    url: '../projectsmain/clearboq',
    
                    dataType: "json",
    
                    data: {projectid:projectid},
    
                    success: function(data){
    
                        if(data.error=='No')
    
                        {
    
                            $('#listboq').trigger('click') ;
    
                        }
    
                    }
    
                });
        }
    
    });
    $(document).on( "click", ".editboqbutton", function(){

        var idval=$(this).attr('data-v');
    
        $('#edititemname'+idval).show();
        
        $('#editslno'+idval).show();
    
        $('#editunitname'+idval).show();
    
        $('#editquantityname'+idval).show();
    
        $('#editratename'+idval).show();
        
        $('#editactivity'+idval).show();
    
        $('#saveboqbutton'+idval).show();
    
        $('#itemtext'+idval).hide();
        
        $('#slnotext'+idval).hide();
    
        $('#unittext'+idval).hide();
    
        $('#ratetext'+idval).hide();
        
        $('#activity'+idval).hide();
    
        $('#quantitytext'+idval).hide();
    
        $('#editboqbutton'+idval).hide();
    
    });
    $(document).on( "click", ".saveboqbutton", function(){

        var idval=$(this).attr('data-v');
    
        var item=$('#edititemname'+idval).val();
        
        var slno=$('#editslno'+idval).val();
    
        var unit=$('#editunitname'+idval).val();
    
        var quantity=$('#editquantityname'+idval).val();

        var str = $('#editratename'+idval).val();
        var rate = str.replace(/,/g, '');

    
        //var rate=$('#editratename'+idval).val();
        
        var activity=$('#editactivity'+idval).val();
    
        var error=0;
    
        $('.error').hide();
    
        if(item=='')
    
        {
    
            $('#edititemname'+idval).next("span").html('Enter Item').show('slow');
    
            error=1;
    
        }
    
        if(unit=='')
    
        {
    
            $('#editunitname'+idval).next("span").html('Enter Unit').show('slow');
    
            error=1;
    
        }
    
        if(quantity=='')
    
        {
    
            $('#editquantityname'+idval).next("span").html('Enter Quantity').show('slow');
    
            error=1;
    
        }
        
        if(activity==0)
    
        {
    
            $('#editactivity'+idval).next("span").html('Select Activity').show('slow');
    
            error=1;
    
        }
    
        if(error==0){
    
            $.ajax({
    
                type: 'POST',
    
                url: '../projectsmain/boqupdate',
    
                beforeSend : function(){
    
                    $('#saveboqbutton'+idval).attr("disabled", true);
    
                },
    
                dataType: "json",
    
                data: {boqid:idval,item:item,unit:unit,quantity:quantity,rate:rate,slno:slno,activity:activity},
    
                success: function(data){
    
                    if(data.error=='No')
    
                    {
    
                        $('#edititemname'+idval).hide();
                        
                        $('#editslno'+idval).hide();
    
                        $('#editunitname'+idval).hide();
    
                        $('#editquantityname'+idval).hide();
    
                        $('#editratename'+idval).hide();
    
                        $('#saveboqbutton'+idval).hide();
    
                        $('#itemtext'+idval).text(item).show();
                        
                        $('#slnotext'+idval).text(slno).show();
    
                        $('#unittext'+idval).text(unit).show();
    
                        $('#ratetext'+idval).text(rate).show();
    
                        $('#quantitytext'+idval).text(quantity).show();
    
                        $('#editboqbutton'+idval).show();
    
                        $('#listboq').trigger('click') ;
    
                    }
    
                    $('#saveboqbutton'+idval).attr("disabled", false);
    
                }
    
            });
    
        }
    
    });

    
 



});
$(document).on('submit','#import_boq_form',function(event){ 
    event.preventDefault();
    $.ajax({
    url: '../projectsmain/import',
    method:"POST",
    data:new FormData(this),
    contentType:false,
    cache:false,
    processData:false,
    beforeSend:function(){
        $('#import_boq').attr('disabled', 'disabled');
        $('#import_boq').val('Importing...');
    },
    success:function(data)
    {
        $('#message').html(data);
        $('#cancel-import_boq').trigger('click') ;
        $('#import_boq_form')[0].reset();
        $('#import_boq').removeAttr('disabled');
        $('#import_boq').val('Import');
    }
    })
});
function getProjectname(id)

{

    var retval;

    $.ajax({

        type: 'POST',

        url: '../projectsmain/getname',

        async:false,

        data: {id:id},

        success: function(data){

            retval=data;

        }

    });

    return retval;

}
