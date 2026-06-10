<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/chart.js" type="text/javascript"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
    function goBack()
    {
        $('#projestiowdashsection').hide('slow');

        $('#projestdashsection').show('slow');
    }
</script>
<style>
div#gauge_div tr {
    background-color: white;
}
div#gaugeestiow_div tr {
    background-color: white;
}
</style>
<h2 class="acc_trigger" id="chartitems"><a href="#">9. Chart</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div id="projestdashsection">
                <div id="estimatedashinfo" class="col-md-10">
                    <p style="text-align: left"> Activity-based view of the project cost</p>
                </div>
                <div class="row show-grid">
                    <div class="col-md-5">
                        <div class="row">
                            <table class="table table-bordered" id="dashpetable" style="display: table; overflow: hidden;">
                                <thead>
                                <tr>
                                    <th>Items of Work</th>
                                    <th colspan="1">Amount</th>
                                </tr>
                                <tr class="preloader" style="display: none;"><td colspan="2" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle" /> </td></tr>
                                </thead>
                                <tbody id="dashpeitems">

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!--<div class="col-md-2"></div>-->
                    <div class="col-md-6" style="right:20px;">
                        <div>
                            <div id="piechart" style="width: 750px; height: 300px;right:30px;"></div>
                            <div class="gauge_chart" style="display: flex;align-items: center;margin-left:180px;">
                                <div id="gauge_div" style="width:400px; height: 220px;"></div>
                            </div>
                        </div>
                        <div class="col-md-12" id="marginper" style="display: none;font-size: medium"></div>
                    </div>
                </div>
            </div>
            <div id="projestiowdashsection" style="display: none">
                <div id="estiowdashinfo" class="col-md-10">

                </div>
                <div class="row show-grid">
                    <div class="col-md-5">
                        <div class="row">
                            <button type="button" value="Back" id="goback" title="back" class="btn btn-primary" style="float: left;width: 100px;margin-bottom:5px " onclick="goBack()">Back</button>
                            <table class="table table-bordered" id="dashestiowtable" style="display: table; overflow: hidden;">
                                <thead>
                                <tr>
                                    <th>Activity</th>
                                    <th colspan="1">Amount</th>
                                </tr>
                                <tr class="preloader" style="display: none;"><td colspan="2" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                                </thead>
                                <tbody id="dashestiowitems">

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6" style="right:20px;">
                        <div>
                            <div id="piechartestiow" style="width: 750px; height: 300px;right:30px;"></div>
                            <div class="gauge_chart" style="display: flex;align-items: center;margin-left:180px;">
                                <div id="gaugeestiow_div" style="width:400px; height: 220px;"></div>
                            </div>
                        </div>
                        <div class="col-md-12" id="marginperestiow" style="display: none;font-size: medium"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>