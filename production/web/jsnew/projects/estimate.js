/**
 * Created by SolmindsDelli5 on 25-09-2017.
 */
$(document).on( "click", ".viewestimate", function(){

    $('#project').removeClass('active').next().slideUp();

    $('#estimateprojtab').addClass('active').next('.acc_container').slideDown();

    var id= $(this).val();

    $('#estimateProject_Id').val(id);

    $("span#projectname h4").html(getProjectname(id));

    $('#listestimateitems').trigger('click') ;
});

$(document).on( "click", "#estimateprojtab", function(){
    if($('#estimateProject_Id').val()!=''){
        $('#project').removeClass('active').next().slideUp();

        $('#estimateprojtab').addClass('active').next('.acc_container').slideDown();

        $("span#projectname h4").html(getProjectname($('#estimateProject_Id').val()));

        $('#listestimateitems').trigger('click') ;
    }
});
$(function(){
    $('#listestimateitems').click(function(){
        $.ajax({
            type: 'POST',
            url: '../projects1/Listestimateitems',
            beforeSend : function(){
                $('.preloaderitems').show();
            },
            dataType: "json",
            data: {projectid:$('#estimateProject_Id').val(),estimatestrucid:$('#estimatestructurelist').val()},
            success: function(data){
                if(data.error=='No')
                {
                    //$('#estimatestructurelist').html(data.result);
                    $('#processwise').html(data.result);
                    $('#projectvalue').val(data.projectvalue);
                    $('#addedproducts').html(data.items);
                    $('#estimatetable').show();
                }

                $('.preloaderitems').hide();
            }
        });
    });
});

$(document).on('change','#processwise',function(){
    var process = $('#processwise').val();
    if(process==1){
        $.ajax({
                type: 'POST',
                url: '../projects1/Listprocesswise',
                beforeSend : function(){
                    $('.preloaderitems').show();
                },
                dataType: "json",
                data: {projectid:$('#estimateProject_Id').val(),process:process},
                success: function(data){
                    if(data.error=='No')
                    {
                        //$('#estimatestructurelist').html(data.result);
                        $('#addedproducts').html(data.result);
                        $('#estimatetable').show();
                    }

                    $('.preloaderitems').hide();
                }
            });
    }
    else{
        $.ajax({
            type: 'POST',
            url: '../projects1/Listestimateitems',
            beforeSend : function(){
                $('.preloaderitems').show();
            },
            dataType: "json",
            data: {projectid:$('#estimateProject_Id').val(),estimatestrucid:$('#estimatestructurelist').val()},
            success: function(data){
                if(data.error=='No')
                {
                    //$('#estimatestructurelist').html(data.result);
                    $('#processwise').html(data.result);
                    $('#addedproducts').html(data.items);
                    $('#estimatetable').show();
                }

                $('.preloaderitems').hide();
            }
        });
    }
    
});    

$(document).on('change','#estimatestructurelist',function(){
    
    var estimatestrucid = $('#estimatestructurelist').val();
    
    $.ajax({
            type: 'POST',
            url: '../projects1/Listestimateitems',
            beforeSend : function(){
                $('.preloaderitems').show();
            },
            dataType: "json",
            data: {projectid:$('#estimateProject_Id').val(),estimatestrucid:estimatestrucid},
            success: function(data){
                if(data.error=='No')
                {
                    $('#estimatestructurelist').html(data.result);
                    $('#addedproducts').html(data.items);
                    $('#estimatetable').show();
                }

                $('.preloaderitems').hide();
            }
        });
    
});

$(document).on('click','.editEstimateiowactivitybutton',function(){
    var activityid=$(this).val();
    
    $('#Estimateiowactivityname'+activityid).hide();
    $('#editEstimateiowactivityname'+activityid).show();
    $('#Estimateiowactivityunit'+activityid).hide();
    $('#editEstimateiowactivityunit'+activityid).show();     
    $('#editEstimateiowactivitybutton'+activityid).hide();
    $('#saveEstimateiowactivitybtn'+activityid).show(); 
    
});

$(document).on('click','.saveEstimateiowactivitybtn',function(){
    var activityid = $(this).val();
    var name = $('#editEstimateiowactivityname'+activityid).val();
    var unit = $('#editEstimateiowactivityunit'+activityid).val();
    
    $.ajax({
            type: 'POST',
            url: '../projects/Estimateworkupdate',
            beforeSend : function(){
                $('#workgroupsearch').attr("disabled", true);
                $('.preloader').show();
            },
            dataType: "json",
            data: {id:activityid,name:name,unit:unit},
            success: function(data){
                if(data.error=='No')
                {
                    $('#Estimateiowactivityname'+activityid).show();
                    $('#Estimateiowactivityname'+activityid).html(data.Name);
                    $('#editEstimateiowactivityname'+activityid).hide();
                    $('#Estimateiowactivityunit'+activityid).html(data.Unit);
                    $('#Estimateiowactivityunit'+activityid).show();
                    $('#editEstimateiowactivityunit'+activityid).hide();     
                    $('#editEstimateiowactivitybutton'+activityid).show();
                    $('#saveEstimateiowactivitybtn'+activityid).hide(); 
                    //$('#estimateprojtab').trigger('click');
                }
                else
                {
                    alert(data.errortext);
                }

                $('#workgroupsearch').attr("disabled", false);
                $('.preloader').hide();
            }
        });
    
});

$(document).on('click','.Estimateiowactdelbtn',function(){
    
    var activityid = $(this).val();
    var r = confirm("Are you sure you want to delete this activity?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../projects/Estimateworkdelete',
            beforeSend : function(){
                $('#Estimateiowactdelbtn'+activityid).attr("disabled", true);
            },
            dataType: "json",
            data: {id:activityid},
            success: function(data){
                if(data.error=='No')
                {
                    $('#Estimateiowactivitiesrow'+data.Id).remove();
                }
                else
                {
                    alert(data.errortext);
                }

                $('#Estimateiowactdelbtn'+data.Id).attr("disabled", false);
            }
        });
    }
    
});

$(document).on( "blur",".quantity", function(){

    alert("hi");//var itemid=$(this).attr('data-id');
    var datatype=$(this).attr('data-type');
    datatype = datatype.replace(/ +/g, "");
    var dataid=$(this).attr('data-id');
    var iowid=$(this).attr('data-iow');
    //console.log(iowid)
    var error=0;
    $('.error').hide();
    if($('#'+datatype+'quantity'+dataid).val()==0)
    {
        $('#'+datatype+'quantity'+dataid).next("span").html('Enter Valid Quantity').show('slow');
        error=1;
    }
    var quantity=($(this).val()*1);
    var specrate=($('#'+datatype+'rate'+dataid).val()*1);
    var amount=specrate*quantity;
    $('#'+datatype+'amount'+dataid).html(amount.toFixed(2));
    $('#amount'+dataid).val(amount);
    var totalrate=0;
    $('.'+datatype+'amount').each(function(){
        //alert($(this).val()*1)
        totalrate=totalrate+($(this).val()*1);
    });

    $('#total'+datatype+'cost').html(totalrate.toFixed(2));
    var totaliowrate=0;
    $('.iowamount'+iowid).each(function(){
        //console.log($(this).val()*1)
        totaliowrate=totaliowrate+($(this).val()*1);
    });

    $('#totaliowcost'+iowid).html(totaliowrate.toFixed(2));
    $('#totaliowcostval'+iowid).val(totaliowrate);
    var totalprojcost=0;
    $('.totaliowcost').each(function(){
        //console.log($(this).val()*1)
        totalprojcost=totalprojcost+($(this).val()*1);
    });
    $('#totalcost').html(totalprojcost.toFixed(2));
});


$(document).on( "click","#saveproduct", function(){
    $.ajax({
        type: 'POST',
        url: '../projects1/Pricingestimate',
        beforeSend : function(){
            $('#saveproduct').attr("disabled", true);
        },
        dataType: "json",
        data: $('#pricingestimateform').serialize(),
        success: function(data){
            if(data.error=='No')
            {
                alert('Saved');
                $('#listestimateitems').trigger('click') ;
            }

            $('#saveproduct').attr("disabled", false);
        }
    });
});

$(document).on( "click",".removeiowitem", function(){
    var actid=$(this).val();

    var r = confirm("Are you sure you want to delete this Activity ?");

    if (r == true) {
        $.ajax({

            type:'POST',

            url:'../projects1/Removeitem',

            beforeSend:function(){

                $('#removeiowitem'+actid).attr("disabled",true);

            },

            dataType:"json",

            data:{actid:actid},

            success:function(data){

                if(data.error=='No')

                {

                    //  $('#tempprodrow'+actid).remove();
                    $('#Estimateiowactivitiesrow'+actid).remove();

                }
                else
                {
                    alert(data.result);
                }

                $('#removeiowitem'+actid).attr("disabled",false);

            }

        });
        $("tr[data-image-id="+actid+"]").remove();
    }
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