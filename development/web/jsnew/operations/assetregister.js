$(document).on( "click", "#AssetregisterTab", function(){
    $('#assetregsearch').trigger('click');
});

$(document).on( "click", "#AssetLibraryItem", function(){
    $('#assetLibItemsearch').trigger('click');
});

$(document).on( "click", "#AssetLibrary", function(){
    $('#assetLibSearch').trigger('click');
});

$(function() { 
    $('#assetregsearch').click(function () {
        $.ajax({
            type: 'POST',
            url: '../report/assetregister',
            beforeSend: function () {
                $('.preloader').show();
            },
            dataType: "json",
            success: function (data) {
                if (data.error == 'No') {
                    $('#assetregisteritems').html(data.result);
                    $('.preloader').hide();
                }
            }
        });
    });

    $('#assetLibItemsearch').click(function () {
        $.ajax({
            type: 'POST',
            url: '../report/assetlibraryitem',
            beforeSend: function () {
                $('.preloader').show();
            },
            dataType: "json",
            success: function (data) {
                if (data.error == 'No') {
                    $('#assetLibitems').html(data.result);
                    $('.preloader').hide();
                }
            }
        });
    });

    $('#assetLibSearch').click(function () {
        $.ajax({
            type: 'POST',
            url: '../report/assetlibrary',
            beforeSend: function () {
                $('.preloader').show();
            },
            dataType: "json",
            success: function (data) {
                if (data.error == 'No') {
                    $('#assetLib').html(data.result);
                    $('.preloader').hide();
                }
            }
        });
    });

$(document).on( "click", "#addAssetLibItemBtn", function(){
    $("#addAssetLibItemPopupTitle").html('Create - Asset Library Item');
   // $('#addAssetLibItemform')[0].reset();
    $("#asset_updateId").val('');
    $("#addAssetLibItemwindow").show();
    $('.error').html('');
});

$(document).on( "click", "#editAssetLibItemBtn", function(){
    $("#addAssetLibItemPopupTitle").html('Edit - Asset Library Item');
    $('#addAssetLibItemform')[0].reset();
    $("#addAssetLibItemwindow").show();
    $('.error').html('');
    itemid = $(this).data('itemid');
    $("#asset_updateId").val(itemid);
    $('#asset_item_name').val($('#asset_item_name_val_'+itemid).val())
    $('#asset_equipment_type').val($('#asset_type_val_'+itemid).val())
});


 

 $(document).on('click','#saveAssetLibItem',function(){  
        var error=0;
        if($('#asset_item_name').val()=='')
        {
            $("#asset_item_name").next("span").html('Enter Asset Item Name').show('slow');
            error=1;
        }
        if($('#asset_equipment_type').val()=='none')
        {
            $("#asset_equipment_type").next("span").html('Select Equipment Type').show('slow');
            error=1;
        }

        if(error==0)
        {
            $.ajax({
                type: 'POST',
                url: '../report/assetlibraryitem',
                beforeSend : function(){
                    $('#saveAssetLibItem').attr("disabled", true);
                },
                dataType: "json",
                data: {
                        updateId    : $('#asset_updateId').val(),
                        action      : 'create', 
                        item_name   : $('#asset_item_name').val(), 
                        type        : $('#asset_equipment_type').val()
                    },
                success: function(data){
                    if(data.error=='No'){
                        $('#addAssetLibItemform')[0].reset();
                        $('#cancelAssetLibItem').trigger('click');
                    }
                    else
                        $("#asset_item_name").next("span").html('Error occurred').show('slow');
                    $('#saveAssetLibItem').attr("disabled", false);
                }
            });
        }
    });
});

 $(document).on('click','#cancelAssetLibItem',function(){  
    $('#addAssetLibItemform')[0].reset();
    //$(this).parents('.tab').removeClass('add-form-active');
    $('#assetLibItemsearch').trigger('click');
    //$('.content-action-wrpr').show();
});
    
