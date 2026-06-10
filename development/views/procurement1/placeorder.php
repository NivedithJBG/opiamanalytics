
<div class="panel panel-default acco-three tab">
    <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/procurement/placeorder.js" type="text/javascript"></script>
    <!--<input type="radio" id="rd1" name="rd">-->
    <div class="panel-heading">
      <h4 class="panel-title " id="choooseplaceorder">
        <a data-toggle="collapse" data-parent="#accordionindex" href="#collapseplaceord">
        <span class="icon-shopping_cart"></span>Place Order</a>
      </h4>
    </div>
    <div id="collapseplaceord" class="tab-content panel-collapse cOrder-body panel-collapse collapse">
        <div class="panel-body">    
            <div class="block">
                <div class="">
                    <div id="cartitemslistsection">
                        <input type="hidden" id="placesearch">
                        <div class="row show-grid">
                            <input type="hidden" id="cartproject" name="project">
                            <form method="GET" action=""  id="cartform">
                          <div class="text-right" style="padding: 15px;">
                             <button type="button"  class="btn btn-primary placeorder placeorder2" id="placeorderss">Place Order</button>
                              <input type="hidden" id="ordeerrs" name="ordeerrs">
                              <input type="hidden" id="restyype" name="restyype">
                              <input type="hidden" id="vendorrs" name="vendorrs">
                              <input type="hidden" id="ordertype" name="ordertype">
                              
                              <input type="hidden" id="cartproject" name="project">
                             </div>
                             <div class="preloader" style="display: none;"><div colspan="10" align="center"><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle"> </div></div>

                             <div class="placeorder-list" id="placeorderdatas" style="">

                                <table class="table table-bordered indent-table" id="ordritemstable" style="display: table; overflow: hidden;">
                            <thead>
                            <!--<tr>
                                <th colspan="9"><span style="float: left;font-weight: bold;padding: 10px;width: 100%;text-align: center">Cart</span></th>
                            </tr>-->

                            <tr>
                                <th></th>
                                <th>Vendor Name</th>
                              
                              <!--   <th>Resource Type</th> -->
                                <th>Resource Name</th>
                                <th>Brand</th>
                                <th>Unit</th>
                                <th>Rate</th>
                                <th>Qnty</th>
                                <th>Amount</th>
                                <th colspan="2">

                                  </th>
                            </tr>
                           
                            </thead>
                            <tbody  id="pordritems">
                            <?php /*echo $datarows;*/?>
                            </tbody>
                        </table>


                     






                    </div>

                       



                                <!-- <div class="" id="cartitemstable">
                                    <div>
                                        
                                        <div class="text-right" style="padding: 15px;  border-bottom: 1px solid #ededed;">
                                            <button type="button"  class="btn btn-primary placeorder placeorder2" id="placeorder">Place Order</button>
                                            <input type="hidden" id="orders" name="orders">
                                            <input type="hidden" id="restype" name="restype">
                                            <input type="hidden" id="vendors" name="vendors">
                                        </div>
                                    </div>
                                    <div class="preloader"><div colspan="10" align="center"><img src="<?php //echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle"> </div></div>
                                
                                    <div class="porderList">
                                        <div class="pOrder-list-cntnr" id="cartitems">
                                            
                                        </div>
                                    <?php /*echo $datarows;*/?>
                                    </div>
                                </div> -->
                            </form>

                            <div id="purchaseorderss"></div>
                            <div id="workorderdatas"></div>
                            <div id="leaseorderdatas"></div>
                            <div id="musterrolldatas"></div>
                            <div id="despatchorderdatas"></div>

                        </div>
                    </div>
                </div>
            </div>    
        </div>
    </div>

    <div class="acc_container" style="display: none;">
        
    </div>

    <script type="text/javascript">

        function placeorderModal(f)
        {
            
            var origin=window.location.origin;
            var pathname=window.location.pathname;
            var pathArray = window.location.pathname.split( '/' );
            var formEl = document.forms.cartform;
            var formData = new FormData(formEl);
            var orders = formData.get('ordeerrs');
            var restype = formData.get('restyype');
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
           
             var formEl = document.forms.cartform;
            var formData = new FormData(formEl);
            var orders = formData.get('ordeerrs');
            var restype = formData.get('restyype');
            var vendorid = formData.get('vendorrs');
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
    <style type="text/css">
        .placeorder{
            text-transform: uppercase;
            border-radius: 2px;
            box-shadow: 0 2px 2px 0 rgba(0, 0, 0, .1);
            background-color: #1b9e43;
        }
        .placeorder:hover,.placeorder:focus,.placeorder:active{
            box-shadow: 0 2px 2px 0 rgba(0, 0, 0, .1);
            background-color: #239a2c;
        }
    </style>

    <!--<div class="placeOrderPop-cntnt">
        <div class="row">
            <div class="col-md-12 approveHdr">
                <h3 id="orderpoptitle">Purchase Order</h3>
                <span class="icon-close"></span>
            </div>
            <iframe id="placeOrderiframe" src="#" style="width:100%; height:540px; border:0px; " ></iframe>         
        </div>
        
    </div>-->
    <!--<h2 class="acc_trigger" id="Cart"><a href="javascript:void(0)">3. Cart</a></h2>-->
    
</div>

