
<div class="panel panel-default /*acco-cart*/ acco-four tab">
    <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/procurement/cart.js" type="text/javascript"></script>
    <!--<input type="radio" id="rd1" name="rd">-->
    <div class="panel-heading">
      <h4 class="panel-title " id="chooseorder">
        <a data-toggle="collapse" data-parent="#accordionindex" href="#collapsecart">
        <span class="icon-shopping_cart"></span>Purchase Orders</a>
      </h4>
    </div>
    <div id="collapsecart" class="tab-content panel-collapse cOrder-body panel-collapse collapse">
        <div class="panel-body">    
            <input type="hidden" id="cartsearch">
            <form id="carttform">
                <div class="placeorder-list">
                    <table class="table table-bordered indent-table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Vendor Name</th>
                                <th>Amount</th>
                                <th colspan="2"></th>
                            </tr>
                       
                        </thead>
                        <tbody id="cartitems">

                        </tbody>
                    </table>
                </div>
            
            </form>

            <div id="purchaseorder"></div>
   
        </div>
    </div>

    <script type="text/javascript">

        function placeorderModal(f)
        {
            
            var origin=window.location.origin;
            var pathname=window.location.pathname;
            var pathArray = window.location.pathname.split( '/' );
            var formEl = document.forms.carttform;
            var formData = new FormData(formEl);
            var orders = formData.get('orders');
            var restype = formData.get('restype');
            //console.log(name)
            var baseUrl=origin+"/"+pathArray[1]+"/"+pathArray[2]+"/order?orders="+orders+"&restype="+restype;
            console.log(baseUrl)
            var form=f,
            modal=$('<div/>', {
                'id':'alert',
                'class': 'customModal confirmorder',
                'html':'<iframe border="0" width="100%" height="100%" src="'+baseUrl+'"></iframe>'
            })
            .dialog({
                show: {
                    effect: "blind",
                    duration: 1000
                },
                'title':'Purchase Order',
                'modal':true,
                'width':1015,
                'height':'700',
                'class': 'customModalDialog confirmorder',
                'buttons': {
                    'OK': function() { 
                    $(this).dialog( "close" ); 
                    // do something, maybe call form.submit();
                    
                    }
                }
                
                
                
            });
            
            return false;
        }
        
        jQuery(document).on('click', '.placeOrderPop-cntnt .icon-close', function(){
            $('.placeOrderPop-cntnt').removeClass('active');
        })
            
        //placeorderModal2();
           
        function placeorderModal2(f) {
            var origin=window.location.origin;
            var pathname=window.location.pathname;

            var pathArray = window.location.pathname.split( '/' );
           
             var formEl = document.forms.carttform;
            var formData = new FormData(formEl);
            var orders = formData.get('orders');
            var restype = formData.get('restype');
            var vendorid = formData.get('vendors');
            if(vendorid==44){
                var vendorname="Company Owned";
            }
            var final = pathname.substring(0,pathname.length-6);
            //alert(final); 
            var baseUrl=origin+final+"/"+"order?orders="+orders+"&restype="+restype;
            //alert(baseUrl);
            console.log(baseUrl)
            console.log(vendorname)
            var ordertype='';
            if(restype==19){   
                ordertype="Work Order";  
            }
            else if(restype==24){ 
                ordertype="Lease Order";  
            }
            else if(restype==33){
                ordertype="Direct Work Order";
            }
            else if( vendorname=="Company Owned" && restype!=33){
                ordertype="Despatch Order";
            }
            else{
                ordertype="Purchase Order";
            }
            $('#orderpoptitle').html(ordertype);
            $('.placeOrderPop-cntnt').addClass('active');
            $('#placeOrderiframe').attr('src', baseUrl);
        }  
        
    </script>
    
</div>

