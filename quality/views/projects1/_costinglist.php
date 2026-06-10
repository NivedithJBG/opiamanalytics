<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/costinglistfunctions.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="costingitems"><a href="javascript:void(0)">6.Estimate</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div id="costinglistiow" class="row show-grid">
                <div class="row show-grid">

                    <div class="col-md-2" id="costingshowprojectname">

                    </div>
                    <div class="col-md-3" id="costingscheduleworkgrouplist">

                    </div>
                </div>
                <form method="POST" action="" id="costingproductform">

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                            <tr>

                                <th>#</th>
                                <th>Item of work</th>
                                <th>Unit</th>
                                <th>Quantity</th>
                                <th>Cost</th>
                                <th></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="6" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="costinglist">
                            <tr><td colspan="6" style="text-align: center">Select Work Group</td></tr>
                            </tbody>
                        </table>
                    </div>



                </form>



            </div>


        </div>


    </div>

</div>