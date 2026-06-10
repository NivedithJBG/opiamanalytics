<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/corporatexp.js" type="text/javascript"></script>

<h2 class="acc_trigger" id="corprtexp" style="display: none;"><a href="javascript:void(0)">2. Corporate Expenditure</a></h2>
    <div class="acc_container">
        <div class="block">
            <div class="jumbotron">
                <div class="row show-grid">
                    <div class="col-md-3" style="display:none;">
                        <button id="corsearch" class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-search"></span>Search</button>
                    </div>
                </div>
                <div id="corsection">
                    <div class="row show-grid">
                        <form>
                            <div id="corinfo" class="col-md-10">

                            </div>
                            <div class="col-md-2" id="printcorexp" style="padding-top: 20px;"></div>
                            <div class="row">
                                <table class="table table-bordered" id="cortable" style="display: table; overflow: hidden;">
                                    <thead>
                                    <tr>
                                        <th>Expense</th>
                                        <th colspan="3">Amount</th>
                                    </tr>
                                    <tr class="preloader" style="display: none;"><td colspan="5" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                                    </thead>
                                    <tbody id="coritems">

                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>