<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/goodsnote.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="grn"><a href="javascript:void(0)">5. Goods Received notes</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <div class="col-md-3"><a href="<?php echo Yii::app()->request->baseUrl; ?>/Operations/create"><button type="button" class="btn btn-success"  id="addrequest"><span class="glyphicon glyphicon-plus-sign"></span>Add GRN</button></a> </div>
                <!--<div class="col-md-3"><button type="button" class="btn btn-success"  id="addproduct"><span class="glyphicon glyphicon-plus-sign"></span>Add Product</button> </div>-->
                <div class="col-md-3"><button type="button" class="btn btn-danger" id="listrequest"><span class="glyphicon glyphicon-list-alt"></span>List GRN</button></div>
            </div>
            <div id="requestlistsection">
                <div id="searchdiv" class="row show-grid" style="display: block;">
                    <div class="col-md-9">
                        <input type="text" placeholder="Search" id="searchrequest" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <button id="requestsearch" class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-search"></span>Search</button>
                    </div>
                </div>
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="requesttable" style="display: table; overflow: hidden;">
                            <?php $user=User::model()->active()->findbyPk(Yii::app()->user->id);if(User::model()->isAdmin() || $user['superuser']==2): ?>
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>User</th>
                                <th >Project</th>
                                <th >Vendor</th>
                                <th >Item</th>
                                <th >Quantity</th>

                                <th colspan="2"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="6" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>

                            <?php endif;?>
                            <tbody id="requestitems">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>