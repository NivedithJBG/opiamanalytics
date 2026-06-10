
<div class="panel panel-default acco-completedorders tab">
    <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/procurement/completedorders.js" type="text/javascript"></script>
    <style type="text/css">
        #Completedorderitems .icon-groups .icon-envelope3 {
        min-height: 31px;
        min-width: 31px;
        border-radius: 50%;
        max-width: 30px;
        padding: 0;
    }
    </style>
    <input type="radio" id="rd1" name="rd">
    <div class="panel-heading">
      <h4 class="panel-title ">
        <a  href="#">
        <span class="icon-shopping_cart"></span>Completed Order</a>
      </h4>
    </div>
    <div  class="tab-content panel-collapse ">
        <div class="panel-body"  id="Completedorderslist">
            <input type="hidden" id="Completedordersearch">
            <div class="porderList">
                <div class="pOrder-list-cntnr" id="Completedorderitems">
                  
                </div>
            </div>
            <table style="display:none;" class="table table-bordered" id="Completedorderstable">
            </table>
            <div class="preloader"><div colspan="9" align="center"><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle"> </div></div>
            <div class="" ></div>
        </div>
    </div>

    <div class="acc_container">
        <div class="block">
            <div class="row show-grid">
                
            </div>
        </div>
    </div>
    <div id="emailorderModel" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Email Order</h4>
                </div>
                <form action="" id="orderemail" method="post">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="email">Email address:</label>
                            <input type="email" class="form-control" id="emailid" required>
                            <span class="error"></span>
                        </div>
                        <div class="form-group">
                            <label for="subject">Subject:</label>
                            <input type="text" class="form-control" id="subject" required>
                            <span class="error"></span>
                        </div>
                        <div class="form-group">
                            <label for="body">Body:</label>
                            <textarea rows="8" cols="25" class="form-control" id="body" required></textarea>
                            <span class="error"></span>
                            <!--<input type="text" class="form-control" id="body" required>-->
                        </div>
                        <div class="mailloader" style="display: none">
                            <img src="<?php echo Yii::$app->request->baseUrl; ?>/images/mail.gif" align="middle">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="orderid">
                        <div class="alert alert-success" id="succesinfo" style="display: none">

                        </div>
                        <div class="alert alert-warning" id="errorinfo" style="display: none">

                        </div>
                        <button type="button" class="btn btn-default" id="emailorder">Send</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
<!--<h2 class="acc_trigger" id="CompletedOrders" style="display: none;"><a href="javascript:void(0)">6. Completed Orders</a></h2>-->