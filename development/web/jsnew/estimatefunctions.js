$(document).on( "click", "#estimate", function(){

    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }
    $('#resourcesection').hide('slow');

});
$(function(){
    setTimeout(function() {
        $("#iowsearch").trigger('click');
    },10);
    $('#addproducts').click(function(){
        $('#productsection').show('slow');
        //$('#transportsection').hide('slow');
        //$('#constructionsection').hide('slow');
        $('#resourcesection').hide('slow');
        /*$('#pisection').hide('slow');
         $('#resourcesection').hide('slow');
         $('#costactivitiessection').hide('slow');
         $('#overheadsection').hide('slow');*/
        $('#productsearch').trigger('click');
    });
    $('.resources').click(function(){
        $('#resourcesection').show('slow');
        $('#productsection').hide('slow');
        var id=$(this).attr('data-id');
        $('#resourcesearch').val(id);
        $('#resourcesearch').trigger('click');
    });
    
    $('#savepiow').click(function(){
        var error=0;
        $('.error').hide();
        if($('#selectworkgroup').val()=='none')
        {
            $('#selectworkgroup').next("span").html('Select Work Group').show('slow');
            error=1;
        }
        if($('#IOW_Name').val()=='')
        {
            $('#IOW_Name').next("span").html('Select Work Group').show('slow');
            error=1;
        }
        if(!IsFloatOnly('#resourceunit'))
        {
            $('#resourceunit').next("span").html('Enter Valid Resource Units').show('slow');
            error=1;
        }
        if(!IsFloatOnly('#workhours'))
        {
            $('#workhours').next("span").html('Enter Valid Work Hours').show('slow');
            error=1;
        }
        if(!IsFloatOnly('#cycles'))
        {
            $('#cycles').next("span").html('Enter Valid Number of Cycles').show('slow');
            error=1;
        }
        $(".activity").each(function() {
            if($(this).val()=='')
            {
                $(this).next("span").html('Enter Activity Name').show('slow');
                error=1;
            }
        })
        $(".bduration").each(function() {
            if(!IsFloatOnly(this))
            {
                $(this).next("span").html('Enter Valid Budgeted Duration').show('slow');
                error=1;
            }
        });
        if($('#IOW_Quantity').val()!='' && !IsFloatOnly($('#IOW_Quantity')))
        {
            $('#IOW_Quantity').next("span").html('Enter Valid Quantity').show('slow');
            error=1;
        }
        /*        $(".eduration").each(function() {
         if(!IsFloatOnly(this))
         {
         $(this).next("span").html('Enter Valid Actual Duration').show('slow');
         error=1;
         }
         });*/
        if(error==1)
        {
            return false;
        }
        else
        {
            return true;
        }


    });
    $(document).on('blur',".bduration",function(){
        $(this).parent().next().find('input.eduration').val($(this).val());
    });
    $('#productsearch').click(function(){
        var name=$('#productname').val();
        $.ajax({
            type: 'POST',
            url: '../../EstimateProject/ProductSearch',
            beforeSend : function(){
                $('#productsearch').attr("disabled", true);
                $('.preloader').show();
            },
            dataType: "json",
            data: {name:name,iowid:$('#IOW_Id').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#productitems').html(data.result);
                }
                else
                {
                    alert(data.errortext);
                }

                $('#productsearch').attr("disabled", false);
                $('.preloader').hide();
            }
        });

    });
    $('#resourcesearch').click(function(){
        var name=$('#resourcename').val();
        var id=$('#resourcesearch').val();
        $.ajax({
            type: 'POST',
            url: '../../EstimateProject/ResourceSearch',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {id:id,iowid:$('#Project_Id').val(),resourcename:name},
            success: function(data){
                if(data.error=='No')
                {
                    $('#resourceitems').html(data.result);
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });

    });
    /*$('#logisticssearch').click(function(){
     var name=$('#logisticsname').val();
     $.ajax({
     type: 'POST',
     url: '../../EstimateProject/LogisticsSearch',
     beforeSend : function(){
     $('#logisticssearch').attr("disabled", true);
     $('.preloader').show();
     },
     dataType: "json",
     data: {name:name,iowid:$('#IOW_Id').val()},
     success: function(data){
     if(data.error=='No')
     {
     $('#logsticsitems').html(data.result);
     }
     else
     {
     alert(data.errortext);
     }

     $('#logisticssearch').attr("disabled", false);
     $('.preloader').hide();
     }
     });

     });

     $('#pisearch').click(function(){
     var name=$('#piname').val();
     $.ajax({
     type: 'POST',
     url: '../../EstimateProject/PiSearch',
     beforeSend : function(){
     $('#pisearch').attr("disabled", true);
     $('.preloader').show();
     },
     dataType: "json",
     data: {name:name,iowid:$('#IOW_Id').val()},
     success: function(data){
     if(data.error=='No')
     {
     $('#piitems').html(data.result);
     }
     else
     {
     alert(data.errortext);
     }

     $('#pisearch').attr("disabled", false);
     $('.preloader').hide();
     }
     });

     });
     $('#costactivitiessearch').click(function(){
     var name=$('#costactivitiesname').val();
     $.ajax({
     type: 'POST',
     url: '../../EstimateProject/ActivitiesSearch',
     beforeSend : function(){
     $('#costactivitiessearch').attr("disabled", true);
     $('.preloader').show();
     },
     dataType: "json",
     data: {name:name,iowid:$('#IOW_Id').val()},
     success: function(data){
     if(data.error=='No')
     {
     $('#costactivitiesitems').html(data.result);
     }
     else
     {
     alert(data.errortext);
     }

     $('#costactivitiessearch').attr("disabled", false);
     $('.preloader').hide();
     }
     });

     });
     $('#overheadsearch').click(function(){
     var name=$('#overheadname').val();
     $.ajax({
     type: 'POST',
     url: '../../EstimateProject/OverheadSearch',
     beforeSend : function(){
     $('#overheadsearch').attr("disabled", true);
     $('.preloader').show();
     },
     dataType: "json",
     data: {name:name,iowid:$('#IOW_Id').val()},
     success: function(data){
     if(data.error=='No')
     {
     $('#overheaditems').html(data.result);
     }
     else
     {
     alert(data.errortext);
     }

     $('#overheadsearch').attr("disabled", false);
     $('.preloader').hide();
     }
     });

     });*/
    /*$('#resourcesearch').click(function(){
     var toshow=$('#selecttype').val();
     var searchval=$('#searchname').val();
     var iowid=$('#IOW_Id').val();
     $.ajax({
     type: 'POST',
     url: '../../EstimateProject/resourcesearch',
     beforeSend : function(){
     $('#resourcesearch').attr("disabled", true);
     $('.preloader').show();
     },
     dataType: "json",
     data: {resourcetype:toshow,resourcename:searchval,iowid:iowid},
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

     $('#resourcesearch').attr("disabled", false);
     $('.preloader').hide();
     }
     });

     });*/

    $(document).on( "blur",".quantity", function(){
        //var itemid=$(this).attr('data-id');
        var datatype=$(this).attr('data-type');
        var dataid=$(this).attr('data-id');
        var error=0;
        $('.error').hide();
        if($('#'+datatype+'quantity'+dataid).val()==0)
        {
            $('#'+datatype+'quantity'+dataid).next("span").html('Enter Valid Quantity').show('slow');
            error=1;
        }
        var quantity=($(this).val()*1);
        var specrate=($('#'+datatype+'specrate'+dataid).val()*1);
        var amount=specrate*quantity;
        $('#'+datatype+'amount'+dataid).html(amount.toFixed(2));
        $('#amount'+dataid).val(amount);
        var totalrate=0;
        $('.iowamount').each(function(){
            //alert($(this).val()*1)
            totalrate=totalrate+($(this).val()*1);
        });

        $('#totalcost').html(totalrate.toFixed(2));
    });
    /*$(document).on( "blur",".specrate", function(){
        var itemid=$(this).attr('data-id');
        var datatype=$(this).attr('data-type');
        var dataid=$(this).attr('data-id');
        var error=0;
        $('.error').hide();
        if($('#'+datatype+'specrate'+dataid).val()==0)
        {
            $('#'+datatype+'specrate'+dataid).next("span").html('Enter Valid Specific Rate').show('slow');
            error=1;
        }
        var specrate=($(this).val()*1);
        var quantity=($('#'+datatype+'quantity'+dataid).val()*1);
        var amount=specrate * quantity;
        $('#'+datatype+'amount'+dataid).html(amount.toFixed(2));
        var totalrate=0;
        $('.iowamount').each(function(){
            totalrate=totalrate+($(this).text()*1);

        });
        $('#totalcost').html(totalrate.toFixed(2));
    });*/

    $(document).on('click','.removeiowitem',function(){
        var productid=$(this).val();
        $.ajax({
            type: 'POST',
            url: '../../EstimateProject/removeproduct',
            beforeSend : function(){
                $('#removeiowitem'+productid).attr("disabled", true);
            },
            dataType: "json",
            data: {productid:productid},
            success: function(data){
                if(data.error=='No')
                {
                    $('#tempprodrow'+productid).remove();
                    $('#totalcost').html(data.cost)
                }
                else
                {
                    alert(data.errortext);
                }

                $('#removeiowitem'+selectedid).attr("disabled", false);
            }
        });
    });
    $(document).on('click','.addproduct',function(){
        var selectedid=$(this).val();
        var rate=$('#productrate'+selectedid).val();
        var quantity=$('#productquantity'+selectedid).val();
        var specrate=$('#productspecrate'+selectedid).val();
        var resgrp=$('#Resource_Grop'+selectedid).val();
        var iow_id=$('#Project_Id').val();
        var activity=$('#iowact'+selectedid).val();
        var error=0;
        $('.error').hide();
        if($('#productquantity'+selectedid).val()==0 )
        {
            $('#productquantity'+selectedid).next("span").html('Enter Valid Quantity').show('slow');
            error=1;
        }
        if(!$.isNumeric($('#productquantity'+selectedid).val()))
        {
            $('#productquantity'+selectedid).next("span").html('Enter Valid Quantity').show('slow');
            error=1;
        }
        if(!$.isNumeric($('#productrate'+selectedid).val()))
        {
            $('#productrate'+selectedid).next("span").html('Enter Valid Rate').show('slow');
            error=1;
        }
        if(error==0)
        {
            $.ajax({
                type: 'POST',
                url: '../../EstimateProject/Addproduct',
                beforeSend : function(){
                    $('#addproduct'+selectedid).attr("disabled", true);
                    $('.preloaderitems').show();
                },
                dataType: "json",
                data: {IOW_id:iow_id,selectedid:selectedid,rate:rate,quantity:quantity,activity:activity,specrate:specrate,resgrp:resgrp},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#addedproducts').html(data.result);
                        $('#productrow'+selectedid).remove();
                    }
                    else
                    {
                        alert(data.errortext);
                    }

                    $('#addproduct'+selectedid).attr("disabled", false);
                    $('.preloaderitems').hide();
                }
            });
        }
    });

    /*$(document).on('click','.addlogistics',function(){
     var selectedid=$(this).val();
     var rate=$('#logisticsrate'+selectedid).val();
     var quantity=$('#logisticsquantity'+selectedid).val();
     var specrate=$('#logisticsspecrate'+selectedid).val();
     var iow_id=$('#IOW_Id').val();
     var activity=$('#iowact'+selectedid).val();
     var error=0;

     $('.error').hide();
     if(!$.isNumeric($('#logisticsquantity'+selectedid).val()))
     {
     $('#logisticsquantity'+selectedid).next("span").html('Enter Valid Quantity').show('slow');
     error=1;
     }
     if($('#logisticsquantity'+selectedid).val()==0 )
     {
     $('#logisticsquantity'+selectedid).next("span").html('Enter Valid Quantity').show('slow');
     error=1;
     }
     if(!$.isNumeric($('#logisticsrate'+selectedid).val()))
     {
     $('#logisticsrate'+selectedid).next("span").html('Enter Valid Rate').show('slow');
     error=1;
     }
     if(error==0)
     {
     $.ajax({
     type: 'POST',
     url: '../../EstimateProject/Addlogistics',
     beforeSend : function(){
     $('#addlogistics'+selectedid).attr("disabled", true);
     $('.preloaderitems').show();
     },
     dataType: "json",
     data: {IOW_id:iow_id,selectedid:selectedid,rate:rate,quantity:quantity,activity:activity,specrate:specrate},
     success: function(data){
     if(data.error=='No')
     {
     $('#addedproducts').html(data.result);
     $('#logisticsrow'+selectedid).remove();
     }
     else
     {
     alert(data.errortext);
     }

     $('#addlogistics'+selectedid).attr("disabled", false);
     $('.preloaderitems').hide();
     }
     });
     }
     });
     $(document).on('click','.addpi',function(){
     var selectedid=$(this).val();
     var rate=$('#pirate'+selectedid).val();
     var quantity=$('#piquantity'+selectedid).val();
     var specrate=$('#pispecrate'+selectedid).val();
     var iow_id=$('#IOW_Id').val();
     var activity=$('#iowact'+selectedid).val();
     var error=0;
     $('.error').hide();
     if(!$.isNumeric($('#piquantity'+selectedid).val()))
     {
     $('#piquantity'+selectedid).next("span").html('Enter Valid Quantity').show('slow');
     error=1;
     }
     if($('#piquantity'+selectedid).val()==0 )
     {
     $('#piquantity'+selectedid).next("span").html('Enter Valid Quantity').show('slow');
     error=1;
     }
     if(!$.isNumeric($('#pirate'+selectedid).val()))
     {
     $('#pirate'+selectedid).next("span").html('Enter Valid Rate').show('slow');
     error=1;
     }
     if(error==0)
     {
     $.ajax({
     type: 'POST',
     url: '../../EstimateProject/Addpi',
     beforeSend : function(){
     $('#addpi'+selectedid).attr("disabled", true);
     $('.preloaderitems').show();
     },
     dataType: "json",
     data: {IOW_id:iow_id,selectedid:selectedid,rate:rate,quantity:quantity,activity:activity,specrate:specrate},
     success: function(data){
     if(data.error=='No')
     {
     $('#addedproducts').html(data.result);
     $('#pirow'+selectedid).remove();
     }
     else
     {
     alert(data.errortext);
     }

     $('#addpi'+selectedid).attr("disabled", false);
     $('.preloaderitems').hide();
     }
     });
     }
     });
     $(document).on('click','.addacitivity',function(){
     var selectedid=$(this).val();
     var rate=$('#acitivityrate'+selectedid).val();
     var quantity=$('#acitivityquantity'+selectedid).val();
     var specrate=$('#acitivityspecrate'+selectedid).val();
     var iow_id=$('#IOW_Id').val();
     var activity=$('#iowact'+selectedid).val();
     var error=0;
     $('.error').hide();
     if($('#acitivityquantity'+selectedid).val()==0 )
     {
     $('#acitivityquantity'+selectedid).next("span").html('Enter Valid Quantity').show('slow');
     error=1;
     }
     if(!$.isNumeric($('#acitivityquantity'+selectedid).val()))
     {
     $('#acitivityquantity'+selectedid).next("span").html('Enter Valid Quantity').show('slow');
     error=1;
     }
     if(!$.isNumeric($('#acitivityrate'+selectedid).val()))
     {
     $('#acitivityrate'+selectedid).next("span").html('Enter Valid Rate').show('slow');
     error=1;
     }
     if(error==0)
     {
     $.ajax({
     type: 'POST',
     url: '../../EstimateProject/Addactivities',
     beforeSend : function(){
     $('#addacitivity'+selectedid).attr("disabled", true);
     $('.preloaderitems').show();
     },
     dataType: "json",
     data: {IOW_id:iow_id,selectedid:selectedid,rate:rate,quantity:quantity,activity:activity,specrate:specrate},
     success: function(data){
     if(data.error=='No')
     {
     $('#addedproducts').html(data.result);
     $('#acitivityrow'+selectedid).remove();
     }
     else
     {
     alert(data.errortext);
     }

     $('#addacitivity'+selectedid).attr("disabled", false);
     $('.preloaderitems').hide();
     }
     });
     }
     });
     $(document).on('click','.addoverhead',function(){
     var selectedid=$(this).val();
     var rate=$('#overheadrate'+selectedid).val();
     var quantity=$('#overheadquantity'+selectedid).val();
     var specrate=$('#overheadspecrate'+selectedid).val();
     var iow_id=$('#IOW_Id').val();
     var activity=$('#iowact'+selectedid).val();
     var error=0;
     $('.error').hide();
     if($('#overheadquantity'+selectedid).val()==0 )
     {
     $('#overheadquantity'+selectedid).next("span").html('Enter Valid Quantity').show('slow');
     error=1;
     }
     if(!$.isNumeric($('#overheadquantity'+selectedid).val()))
     {
     $('#overheadquantity'+selectedid).next("span").html('Enter Valid Quantity').show('slow');
     error=1;
     }
     if(!$.isNumeric($('#overheadrate'+selectedid).val()))
     {
     $('#overheadrate'+selectedid).next("span").html('Enter Valid Rate').show('slow');
     error=1;
     }
     if(error==0)
     {
     $.ajax({
     type: 'POST',
     url: '../../EstimateProject/Addoverheads',
     beforeSend : function(){
     $('#addoverhead'+selectedid).attr("disabled", true);
     $('.preloaderitems').show();
     },
     dataType: "json",
     data: {IOW_id:iow_id,selectedid:selectedid,rate:rate,quantity:quantity,activity:activity,specrate:specrate},
     success: function(data){
     if(data.error=='No')
     {
     $('#addedproducts').html(data.result);
     $('#overheadrow'+selectedid).remove();
     }
     else
     {
     alert(data.errortext);
     }

     $('#addoverhead'+selectedid).attr("disabled", false);
     $('.preloaderitems').hide();
     }
     });
     }
     });*/
    $(document).on('click','.addresource',function(){
        var selectedid=$(this).val();
        var restype=$('#resourcetype'+selectedid).val();
        var rate=$('#specificrate'+selectedid).val();
        var quantity=$('#resourcequantity'+selectedid).val();
        var iow_id=$('#IOW_Id').val();
        var activity=$('#iowact'+selectedid).val();
        var error=0;
        $('.error').hide();
        if(!$.isNumeric($('#resourcequantity'+selectedid).val()))
        {
            $('#resourcequantity'+selectedid).next("span").html('Enter Valid Quantity').show('slow');
            error=1;
        }
        if($('#resourcequantity'+selectedid).val()==0 )
        {
            $('#resourcequantity'+selectedid).next("span").html('Enter Valid Quantity').show('slow');
            error=1;
        }
        if(!$.isNumeric($('#specificrate'+selectedid).val()))
        {
            $('#specificrate'+selectedid).next("span").html('Enter Valid Rate').show('slow');
            error=1;
        }
        if(error==0)
        {
            $.ajax({
                type: 'POST',
                url: '../../EstimateProject/Resourceadd',
                beforeSend : function(){
                    $('#addresourcebutton'+selectedid).attr("disabled", true);
                    $('.preloaderitems').show();
                },
                dataType: "json",
                data: {IOW_id:iow_id,selectedid:selectedid,rate:rate,quantity:quantity,activity:activity,restype:restype},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#addedproducts').html(data.result);
                        $('#resourcerow'+selectedid).remove();
                    }
                    else
                    {
                        alert(data.errortext);
                    }

                    $('#addresourcebutton'+selectedid).attr("disabled", false);
                    $('.preloaderitems').hide();
                }
            });
        }
    });

    $(document).on('click','.addmaterial',function(){
        var selectedid=$(this).val();
        var rate=$('#materialrate'+selectedid).val();
        var quantity=$('#materialquantity'+selectedid).val();
        var iow_id=$('#IOW_Id').val();
        var error=0;

        $('.error').hide();
        if(!$.isNumeric($('#materialquantity'+selectedid).val()))
        {
            $('#materialquantity'+selectedid).next("span").html('Enter Valid Quantity').show('slow');
            error=1;
        }
        if(!$.isNumeric($('#materialrate'+selectedid).val()))
        {
            $('#materialrate'+selectedid).next("span").html('Enter Valid Rate').show('slow');
            error=1;
        }
        if(error==0)
        {
            $.ajax({
                type: 'POST',
                url: '../../EstimateProject/AddPMT',
                beforeSend : function(){
                    $('#addmaterial'+selectedid).attr("disabled", true);
                    $('.preloaderitems').show();
                },
                dataType: "json",
                data: {IOW_id:iow_id,selectedid:selectedid,rate:rate,quantity:quantity},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#addedproducts').html(data.result);
                        $('#materialrow'+selectedid).remove();
                    }
                    else
                    {
                        alert(data.errortext);
                    }

                    $('#addmaterial'+selectedid).attr("disabled", false);
                    $('.preloaderitems').hide();
                }
            });
        }
    });


    // Resource search transport

    $('#transportresourcesearch').click(function(){
        var searchval=$('#transportresource').val();
        var transtype=$('#transportselecttype').val();
        var vendor=$('#transportselectvendor').val();
        $.ajax({
            type: 'POST',
            url: '../../EstimateProject/PMTSearch',
            beforeSend : function(){
                $('#transportresourcesearch').attr("disabled", true);
                $('.preloader').show();
            },
            dataType: "json",
            data: {resourcetype:transtype,vendor:vendor,resourcename:searchval,type:'transport'},
            success: function(data){
                if(data.error=='No')
                {
                    $('#tranportresourceitems').html(data.result);
                }
                else
                {
                    alert(data.errortext);
                }

                $('#transportresourcesearch').attr("disabled", false);
                $('.preloader').hide();
            }
        });
    });



    $(document).on( "click", ".transportaddresource", function(){
        var resid=$(this).val();
        var error=0;
        $('.error').hide();
        if(!$.isNumeric($('#transportresourcequantity'+resid).val()))
        {
            $('#transportresourcequantity'+resid).next("span").html('Enter Valid Quantity').show('slow');
            error=1;
        }
        if(!$.isNumeric($('#materialrate'+resid).val()))
        {
            $('#materialrate'+resid).next("span").html('Enter Valid Rate').show('slow');
            error=1;
        }
        if(error==0)
        {
            var price=$('#transportresourceprice'+resid).val()
            var quantity=$('#transportresourcequantity'+resid).val()
            var iow_id=$('#IOW_Id').val();
            $.ajax({
                type:'POST',
                url:'../../EstimateProject/Addresource',
                beforeSend:function(){
                    $('#transportaddresourcebutton'+resid).attr("disabled",true);
                    $('.preloaderitems').show();
                },
                dataType:"json",
                data:{iow_id:iow_id,resid:resid,rate:price,quantity:quantity,type:'Transport'},
                success:function(data){
                    $('#addedproducts').html(data.result);
                    $('.preloaderitems').hide();
                    $('#transportresourcerow'+resid).remove();

                }
            });
        }
    });


    // Resource search concstruct

    $('#constructresourcesearch').click(function(){
        var searchval=$('#searchname').val();
        var transtype=$('#constructselecttype').val();
        var vendor=$('#constructselectvendor').val();
        $.ajax({
            type: 'POST',
            url: '../../EstimateProject/resourcesearch',
            beforeSend : function(){
                $('#constructresourcesearch').attr("disabled", true);
                $('.preloader').show();
            },
            dataType: "json",
            data: {vendor:vendor,resourcetype:transtype,resourcename:searchval,type:'construct'},
            success: function(data){
                if(data.error=='No')
                {
                    $('#constructresourceitems').html(data.result);
                }
                else
                {
                    alert(data.errortext);
                }

                $('#constructresourcesearch').attr("disabled", false);
                $('.preloader').hide();
            }
        });
    });



    $(document).on( "click", ".constructaddresource", function(){
        var resid=$(this).val()
        var error=0;

        $('.error').hide();
        if(!$.isNumeric($('#constructresourcequantity'+resid).val()))
        {
            $('#constructresourcequantity'+resid).next("span").html('Enter Valid Quantity').show('slow');
            error=1;
        }
        if(error==0)
        {
            var price=$('#constructresourceprice'+resid).val()
            var quantity=$('#constructresourcequantity'+resid).val()
            var iow_id=$('#IOW_Id').val();
            $.ajax({
                type:'POST',
                url:'../../EstimateProject/Addresource',
                beforeSend:function(){
                    $('#constructaddresourcebutton'+resid).attr("disabled",true);
                    $('.preloaderitems').show();
                },
                dataType:"json",
                data:{iow_id:iow_id,resid:resid,rate:price,quantity:quantity,type:'Constuction'},
                success:function(data){
                    $('#addedproducts').html(data.result);
                    $('.preloaderitems').hide();
                    $('#constructresourcerow'+resid).remove();
                    $('#constructaddresourcebutton'+resid).attr("disabled",false);

                }
            });
        }
    });

    $('#saveproduct').click(function(){
        var error=0
        if($('#selectworkgroup').val()=='none')
        {
            $('#selectworkgroup').next("span").html('Select Work Group').show('slow');
            error=1;
        }
        if($('#IOW_Name').val()=='')
        {
            $('#IOW_Name').next("span").html('Enter Name').show('slow');
            error=1;
        }
        if(error==0)
        {
            $('#productform').submit();
        }
    });

    $('#iowsearch').click(function(){
        var projectid=$('#projectid').val();
        var workgroupid=$('#workgroupid').val();
        var iowname=$('#searchname').val();
        $.ajax({
            type: 'POST',
            url: '../EstimateProject/search',
            beforeSend : function(){
                $('#iowsearch').attr("disabled", true);
                $('.preloader').show();
            },
            dataType: "json",
            data: {projectid:projectid,workgroupid:workgroupid,iowname:iowname},
            success: function(data){
                if(data.error=='No')
                {
                    $('#iowitems').html(data.result);
                    $('#iowtable').show();
                }
                else
                {
                    alert(data.errortext);
                }

                $('#iowsearch').attr("disabled", false);
                $('.preloader').hide();
            }
        });
    });
    $('#addmore').click(function(){
        var newRowContent='<tr><td><input type="text" class="form-control activity"  name="activity[]"/><span class="error"></span></td><td><input type="text" class="form-control bduration"  name="bduration[]"/><span class="error"></span></td><td><input type="text" class="form-control eduration"  name="eduration[]"/><span class="error"></span><input type="hidden" name="fresshness[]" value="New"></td><td><button type="button" class="btn btn-primary removeitem" title="Remove Activity"><span class="glyphicon glyphicon-minus"></span></button></td></tr>'
        $("#activitytable tbody").append(newRowContent);
    });
    $(document).on( "click", ".removeitem", function(){
        $(this).closest("tr").remove();
    });

    $( "#addedproducts" ).sortable({

        deactivate:function(event, ui){
            //alert('test')
        },
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
                url: '../../EstimateProject/updatesort',
                data: {datavalue:updatedrows},
                dataType: "json",
                success: function(data){}
            });
        }

    }).disableSelection()
});

function ResourceTypeNameExist(name)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../resourcetype/checkname',
        async:false,
        data: {name:name},
        success: function(data){
            retval=data;
        }
    });
    return retval;

}

$(document).on('focus','#transportresource',function(){
    var restype=$('#transportselecttype').val();
    var vendor=$('#transportselectvendor').val();
    $.ajax({
        type: 'POST',
        url: '../../resources/checkResourceName',
        async:false,
        dataType: "json",
        data: {type:restype,vendor:vendor},
        success: function(data){
            var names=[];
            var i=0
            $.each(data, function(idx, obj) {
                /*names[i]='"'+data[idx]+'"';*/
                names[i]=data[idx];
                i++
            });
            $( "#transportresource" ).autocomplete({
                source: names
            });

        }
    });

});
$(document).on('focus','#constructresource',function(){
    var restype=$('#constructselecttype').val();
    var vendor=$('#constructselectvendor').val();
    $.ajax({
        type: 'POST',
        url: '../../resources/checkResourceName',
        async:false,
        dataType: "json",
        data: {type:restype,vendor:vendor},
        success: function(data){
            var names=[];
            var i=0
            $.each(data, function(idx, obj) {
                /*names[i]='"'+data[idx]+'"';*/
                names[i]=data[idx];
                i++
            });
            $( "#constructresource" ).autocomplete({
                source: names
            });

        }
    });

});


$(document).on('click','.deleteiowbutton',function(){
    var iowid=$(this).val();
    var r = confirm("Are you sure you want to delete this Item Of Work?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../EstimateProject/deleteiow',
            beforeSend : function(){
                $('#deleteiowbutton'+iowid).attr("disabled", true);
            },
            dataType: "json",
            data: {iowid:iowid},
            success: function(data){
                if(data.error=='No')
                {
                    $('#iowrow'+data.Id).remove();
                }
                else
                {
                    alert(data.errortext);
                }

                $('#deleteiowbutton'+data.Id).attr("disabled", false);
            }
        });
    }
});
function IsFloatOnly(element) {
    var value = $(element).val();
    var regExp ="^\\d+(\\.\\d+)?$";
    return value.match(regExp);
}
$(document).on('click','#editIOW',function(){
    $('#activityview').toggle('fast');
    $('#activityedit').toggle('slow');
    $('#IOWtitle').text('Edit Item Of Work Activities');
});
$(document).on('click','#canceledit',function(){
    $('#activityedit').toggle('fast');
    $('#activityview').toggle('slow');
    $('#IOWtitle').text('View Item Of Work Activities');
});
$(document).on('click','.removeitemadded',function(){
    var id=$(this).val();
    var r = confirm("Are you sure you want to delete this Activity?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../../EstimateProject/deleteactivity',
            beforeSend : function(){
                $('#removeitemadded'+id).attr("disabled", true);
            },
            dataType: "json",
            data: {activityid:id},
            success: function(data){
                if(data.error=='No')
                {
                    $('#removeitemadded'+id).closest("tr").remove();
                }
                else
                {
                    alert(data.errortext);
                }

                $('#removeitemadded'+id).attr("disabled", false);
            }
        });
    }
});
$(document).on('keypress',".removeitem",function(event){
    if (event.keyCode == 10 || event.keyCode == 13 )
        event.preventDefault();
});/*
 $('#comment').keypress(function(event){

 if (event.keyCode == 10 || event.keyCode == 13)
 event.preventDefault();

 });
 */

$(document).on('mouseenter','.hover',function(){
    var tooltip=$(this).attr('data-tooltip');
    $('.tooltiptable').hide();
    $('#'+tooltip).fadeIn('fast');
});
$(document).on('mouseleave','.hover',function(){
    var tooltip=$(this).attr('data-tooltip');
    $('#'+tooltip).fadeOut('slow');
});
