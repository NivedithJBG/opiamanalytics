<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/financedashboard.js" type="text/javascript"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
    function goBack()
    {
        $('#accountspiesection').hide('slow');
        $('#accountsgaugesection').hide('slow');

        $('#findashboardsection').show('slow');
    }
    function goBackAcnt()
    {
        $('#findashboardsection').hide('slow');
        $('#accountsgaugesection').hide('slow');
        $('#accountspiesection').show('slow');
    }
</script>
<script>
    $(document).on('click','.hover',function(){
        var tooltip=$(this).attr('data-tooltip');
        $('.tooltiptable').hide();
        $('#'+tooltip).fadeIn('fast');
    });
    $(document).on('mouseleave','.hover',function(){
        var tooltip=$(this).attr('data-tooltip');
        $('#'+tooltip).fadeOut('slow');
    });
</script>
<style>
    .chartstyle{
        /*width: 600px; height: 400px;margin-left: -65px;*/
        width: auto !important; height: 400px;margin-left: -82px;
    }
    .chartstyle1{
        /*width: 600px; height: 400px;*/
        width: auto !important;; height: 400px;margin-left: -70px;
    }
    .columnchart {
        width:450px;
        height: 250px;
    }
    .acnthdcolumnchart {
        width:450px;
        height: 250px;
    }
    div#gauge_div td > div >  div {
        margin: 0 auto;
        margin-left: 129px;
        left: 42px;
    }
    div.columnchart td > div >  div {
        margin: 0 auto;
        /*margin-left: 129px;
        left: 42px;*/
    }
    div.acnthdcolumnchart td > div >  div {
        margin: 0 auto;
        /*margin-left: 129px;
        left: 42px;*/
    }
    div.columnchart tr {
        background-color:white;
    }
    div.acnthdcolumnchart tr {
        background-color:white;
    }
    div#gauge_div tr{
        background-color:white;
    }
    .gauge_entries {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    .gauge_entries p {
        font-size: 14px;
        margin-bottom: 0 !important;
        padding-bottom: 0 !important;
    }

    .gauge_entries p.dot:before {
        content: "";
        background: green;
        display: inline-block;
        width: 13px;
        height: 13px;
        border-radius: 50%;
        position: relative;
        top: 1px;
    }

    .gauge_entries p.dot.green:before {
        background: green;
    }

    .gauge_entries p.dot.yellow:before {
        background: #ff9907;
    }
    
    .gauge_entries p.dot.red:before {
        background: #dc3911;
    }
    #gauge_div g > g path {
        fill: black;
        stroke: black;
    }
    .columnchart g > g path {
        fill: black;
        stroke: black;
    }
    .acnthdcolumnchart g > g path {
        fill: black;
        stroke: black;
    }
    .estamt,.actamt,.estamtacnthd,.actamtacnthd {
        display: none;
    }
    .acnthdcolumnchart g > g text {
        font-size: 19px !important;
    }
    .columnchart g > g text {
        font-size: 19px !important;
    }
</style>
<h2 class="acc_trigger" id="financedashboard"><a href="javascript:void(0)">1. Dashboard</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <div class="col-md-5">
                    <select class="form-control" id="findashproject" name="project">
                        <option value="0">Select Project</option>
                        <?php $project=Projects::model()->findAll(array('condition'=>'Project_Delete_Status=0'));
                        foreach($project AS $list):
                            if($list->Project_Id==36):
                                $selected="selected";
                            else:
                                $selected="";
                            endif;
                            echo "<option value='".$list->Project_Id."' ".$selected.">".$list->Name."</option>";
                        endforeach;?>
                    </select>
                    <span class='error' style="float: left;"></span>
                </div>
                <div class="col-md-3">
                    <button id="findashboardsearch" class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-search"></span>Search</button>
                </div>
            </div>

            <div id="findashboardsection" >
                <div id="pedashinfo" class="col-md-10">

                </div>
                <div class="row show-grid">
                    <!--<div class="col-md-12 preloader" align="center" style="display: none;"><img src="<?php /*echo Yii::app()->request->baseUrl; */?>/images/loader.gif" align="middle"></div>-->

                    <div class="col-md-5">
                        <div class="row">
                            <table class="table table-bordered" id="dashpetable" style="display: table; overflow: hidden;">
                                <thead>
                                <tr>
                                    <th>Expense</th>
                                    <th colspan="1">Amount</th>
                                </tr>
                                <tr class="preloader" style="display: none;"><td colspan="2" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                                </thead>
                                <tbody id="dashpeitems">

                                </tbody>
                            </table>
                            <table class="table" id="dashpeincometable" style="display: table; overflow: hidden;">
                                <tbody id="dashpeincomeitems">

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!--<div class="col-md-1"></div>-->
                    <div class="col-md-6">
                        <div>
                        <!--<div id="piechart" style="width: 750px; height: 450px;right:30px;"></div>-->
                        <div id="piechart" style="width: 750px; height: 400px;margin-left:70px;"></div>
                            <!--<div style="margin-right: 112px;margin-bottom: -2px;"><b>Estimate cost v/s Actual cost</b></div>-->
                            <div class="gauge_chart" style="display: flex;align-items: center;">
                                <div id="gauge_div" style="width:400px; height: 220px;"></div>
                                <!--<div class="gauge_entries">
                                    <p class="dot green">
                                       Estimated Project Cost
                                    </p>
                                    <p class="dot yellow">
                                        Profit
                                    </p>
                                    <p class="dot red">
                                        Expenditure
                                    </p>
                                </div>-->
                            </div>    
                        </div>
                        <div class="col-md-12" id="marginper" style="display: none;font-size: medium"></div>
                    </div>     
                    <!--<div id="marginper" style="display: none"></div>-->
                </div>
                <div class="row show-grid">
                    <div class="col-md-4" style="text-align: center">
                        <div class="columnchart chartstyle" id="columnchart0"></div>
                        <span id="actsbgrpname0" class="estamt" style="font-size: 16px;font-weight: bold"></span><br>
                        <span id="estamt0" class="estamt">E.amount</span><br>
                        <span id="actamt0" class="actamt">A.amount</span>
                    </div>
                    <div class="col-md-4" style="text-align: center">
                        <div class="columnchart chartstyle1" id="columnchart1"></div>
                        <span id="actsbgrpname1" class="estamt" style="font-size: 16px;font-weight: bold"></span><br>
                        <span id="estamt1" class="estamt">E.amount</span><br>
                        <span id="actamt1" class="actamt">A.amount</span>
                    </div>
                    <div class="col-md-4" style="text-align: center">
                        <div class="columnchart chartstyle" id="columnchart2"></div>
                        <span id="actsbgrpname2" class="estamt" style="font-size: 16px;font-weight: bold"></span><br>
                        <span id="estamt2" class="estamt">E.amount</span><br>
                        <span id="actamt2" class="actamt">A.amount</span>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-md-4" style="text-align: center">
                        <div class="columnchart chartstyle" id="columnchart3"></div>
                        <span id="actsbgrpname3" class="estamt" style="font-size: 16px;font-weight: bold"></span><br>
                        <span id="estamt3" class="estamt">E.amount</span><br>
                        <span id="actamt3" class="actamt">A.amount</span>
                    </div>
                    <div class="col-md-4" style="text-align: center">
                        <div class="columnchart chartstyle1" id="columnchart4"></div>
                        <span id="actsbgrpname4" class="estamt" style="font-size: 16px;font-weight: bold"></span><br>
                        <span id="estamt4" class="estamt">E.amount</span><br>
                        <span id="actamt4" class="actamt">A.amount</span>
                    </div>
                    <div class="col-md-4" style="text-align: center">
                        <div class="columnchart chartstyle" id="columnchart5"></div>
                        <span id="actsbgrpname5" class="estamt" style="font-size: 16px;font-weight: bold"></span><br>
                        <span id="estamt5" class="estamt">E.amount</span><br>
                        <span id="actamt5" class="actamt">A.amount</span>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-md-4" style="text-align: center">
                        <div class="columnchart chartstyle" id="columnchart6"></div>
                        <span id="actsbgrpname6" class="estamt" style="font-size: 16px;font-weight: bold"></span><br>
                        <span id="estamt6" class="estamt">E.amount</span><br>
                        <span id="actamt6" class="actamt">A.amount</span>
                    </div>
                    <div class="col-md-4" style="text-align: center">
                        <div class="columnchart chartstyle1" id="columnchart7"></div>
                        <span id="actsbgrpname7" class="estamt" style="font-size: 16px;font-weight: bold"></span><br>
                        <span id="estamt7" class="estamt">E.amount</span><br>
                        <span id="actamt7" class="actamt">A.amount</span>
                    </div>
                    <div class="col-md-4" style="text-align: center">
                        <div class="columnchart chartstyle" id="columnchart8"></div>
                        <span id="actsbgrpname8" class="estamt" style="font-size: 16px;font-weight: bold"></span><br>
                        <span id="estamt8" class="estamt">E.amount</span><br>
                        <span id="actamt8" class="actamt">A.amount</span>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-md-4" style="text-align: center">
                        <div class="columnchart chartstyle" id="columnchart9"></div>
                        <span id="actsbgrpname9" class="estamt" style="font-size: 16px;font-weight: bold"></span><br>
                        <span id="estamt9" class="estamt">E.amount</span><br>
                        <span id="actamt9" class="actamt">A.amount</span>
                    </div>
                    <div class="col-md-4" style="text-align: center">
                        <div class="columnchart chartstyle1" id="columnchart10"></div>
                        <span id="actsbgrpname10" class="estamt" style="font-size: 16px;font-weight: bold"></span><br>
                        <span id="estamt10" class="estamt">E.amount</span><br>
                        <span id="actamt10" class="actamt">A.amount</span>
                    </div>
                    <div class="col-md-4" style="text-align: center">
                        <div class="columnchart chartstyle" id="columnchart11"></div>
                        <span id="actsbgrpname11" class="estamt" style="font-size: 16px;font-weight: bold"></span><br>
                        <span id="estamt11" class="estamt">E.amount</span><br>
                        <span id="actamt11" class="actamt">A.amount</span>
                    </div>
                </div>
            </div>
            <div id="accountspiesection" style="display: none">
                <div id="acntgrpdashinfo" class="col-md-10">

                </div>
                <div class="row show-grid">
                    <div class="col-md-5">
                        <div class="row">
                            <button type="button" value="Back" id="goback" title="back" class="btn btn-primary" style="float: left;width: 100px;margin-bottom:5px " onclick="goBack()">Back</button>
                            <table class="table table-bordered" id="dashpeacnthdtable" style="display: table; overflow: hidden;">
                                <thead>
                                <tr>
                                    <th>Expense</th>
                                    <th colspan="1">Amount</th>
                                </tr>
                                <tr class="preloader" style="display: none;"><td colspan="2" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                                </thead>
                                <tbody id="dashpeacnthditems">

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-1">

                    </div>
                    <div class="col-md-6">
                        <div class="columnchart chartstyle1" id="piechartacnthd"></div>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-md-4">
                        <div class="acnthdcolumnchart chartstyle" id="acnthdcolumnchart0"></div>
                        <span id="acnthdname0" class="estamtacnthd" style="font-size: 16px;font-weight: bold"></span><br>
                        <span id="estamtacnthd0" class="estamtacnthd">E.amount</span><br>
                        <span id="actamtacnthd0" class="estamtacnthd">A.amount</span>
                    </div>
                    <div class="col-md-4">
                        <div class="acnthdcolumnchart chartstyle1" id="acnthdcolumnchart1"></div>
                        <span id="acnthdname1" class="estamtacnthd" style="font-size: 16px;font-weight: bold"></span><br>
                        <span id="estamtacnthd1" class="estamtacnthd">E.amount</span><br>
                        <span id="actamtacnthd1" class="estamtacnthd">A.amount</span>
                    </div>
                    <div class="col-md-4">
                        <div class="acnthdcolumnchart chartstyle" id="acnthdcolumnchart2"></div>
                        <span id="acnthdname2" class="estamtacnthd" style="font-size: 16px;font-weight: bold"></span><br>
                        <span id="estamtacnthd2" class="estamtacnthd">E.amount</span><br>
                        <span id="actamtacnthd2" class="estamtacnthd">A.amount</span>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-md-4">
                        <div class="acnthdcolumnchart chartstyle" id="acnthdcolumnchart3"></div>
                        <span id="acnthdname3" class="estamtacnthd" style="font-size: 16px;font-weight: bold"></span><br>
                        <span id="estamtacnthd3" class="estamtacnthd">E.amount</span><br>
                        <span id="actamtacnthd3" class="estamtacnthd">A.amount</span>
                    </div>
                    <div class="col-md-4">
                        <div class="acnthdcolumnchart chartstyle1" id="acnthdcolumnchart4"></div>
                        <span id="acnthdname4" class="estamtacnthd" style="font-size: 16px;font-weight: bold"></span><br>
                        <span id="estamtacnthd4" class="estamtacnthd">E.amount</span><br>
                        <span id="actamtacnthd4" class="estamtacnthd">A.amount</span>
                    </div>
                    <div class="col-md-4">
                        <div class="acnthdcolumnchart chartstyle" id="acnthdcolumnchart5"></div>
                        <span id="acnthdname5" class="estamtacnthd" style="font-size: 16px;font-weight: bold"></span><br>
                        <span id="estamtacnthd5" class="estamtacnthd">E.amount</span><br>
                        <span id="actamtacnthd5" class="estamtacnthd">A.amount</span>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-md-4">
                        <div class="acnthdcolumnchart chartstyle" id="acnthdcolumnchart6"></div>
                        <span id="acnthdname6" class="estamtacnthd" style="font-size: 16px;font-weight: bold"></span><br>
                        <span id="estamtacnthd6" class="estamtacnthd">E.amount</span><br>
                        <span id="actamtacnthd6" class="estamtacnthd">A.amount</span>
                    </div>
                    <div class="col-md-4">
                        <div class="acnthdcolumnchart chartstyle1" id="acnthdcolumnchart7"></div>
                        <span id="acnthdname7" class="estamtacnthd" style="font-size: 16px;font-weight: bold"></span><br>
                        <span id="estamtacnthd7" class="estamtacnthd">E.amount</span><br>
                        <span id="actamtacnthd7" class="estamtacnthd">A.amount</span>
                    </div>
                    <div class="col-md-4">
                        <div class="acnthdcolumnchart chartstyle" id="acnthdcolumnchart8"></div>
                        <span id="acnthdname8" class="estamtacnthd" style="font-size: 16px;font-weight: bold"></span><br>
                        <span id="estamtacnthd8" class="estamtacnthd">E.amount</span><br>
                        <span id="actamtacnthd8" class="estamtacnthd">A.amount</span>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-md-4">
                        <div class="acnthdcolumnchart chartstyle" id="acnthdcolumnchart9"></div>
                        <span id="acnthdname9" class="estamtacnthd" style="font-size: 16px;font-weight: bold"></span><br>
                        <span id="estamtacnthd9" class="estamtacnthd">E.amount</span><br>
                        <span id="actamtacnthd9" class="estamtacnthd">A.amount</span>
                    </div>
                    <div class="col-md-4">
                        <div class="acnthdcolumnchart chartstyle1" id="acnthdcolumnchart10"></div>
                        <span id="acnthdname10" class="estamtacnthd" style="font-size: 16px;font-weight: bold"></span><br>
                        <span id="estamtacnthd10" class="estamtacnthd">E.amount</span><br>
                        <span id="actamtacnthd10" class="estamtacnthd">A.amount</span>
                    </div>
                    <div class="col-md-4">
                        <div class="acnthdcolumnchart chartstyle" id="acnthdcolumnchart11"></div>
                        <span id="acnthdname11" class="estamtacnthd" style="font-size: 16px;font-weight: bold"></span><br>
                        <span id="estamtacnthd11" class="estamtacnthd">E.amount</span><br>
                        <span id="actamtacnthd11" class="estamtacnthd">A.amount</span>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-md-4">
                        <div class="acnthdcolumnchart chartstyle" id="acnthdcolumnchart12"></div>
                    </div>
                    <div class="col-md-4">
                        <div class="acnthdcolumnchart chartstyle1" id="acnthdcolumnchart13"></div>
                    </div>
                    <div class="col-md-4">
                        <div class="acnthdcolumnchart chartstyle" id="acnthdcolumnchart14"></div>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-md-4">
                        <div class="acnthdcolumnchart chartstyle" id="acnthdcolumnchart15"></div>
                    </div>
                    <div class="col-md-4">
                        <div class="acnthdcolumnchart chartstyle1" id="acnthdcolumnchart16"></div>
                    </div>
                    <div class="col-md-4">
                        <div class="acnthdcolumnchart chartstyle" id="acnthdcolumnchart17"></div>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-md-4">
                        <div class="acnthdcolumnchart chartstyle" id="acnthdcolumnchart18"></div>
                    </div>
                    <div class="col-md-4">
                        <div class="acnthdcolumnchart chartstyle1" id="acnthdcolumnchart19"></div>
                    </div>
                </div>
            </div>
            <div id="accountsgaugesection" style="display: none;">
                <div id="pedashaciteminfo" class="col-md-10">

                </div>
                <div class="row show-grid">
                    
                    <div class="col-md-5">
                        <div class="row"> 
                            <button type="button" value="Back" id="gobacktoacnt" title="back" class="btn btn-primary" style="float: left;width: 100px;margin-bottom:5px " onclick="goBackAcnt()">Back</button>
                            <table class="table table-bordered" id="dashpeitemstable" style="display: table; overflow: hidden;">
                                <thead>
                                <tr>
                                    <th>Expense</th>
                                    <th colspan="1">Amount</th>
                                </tr>
                                <tr class="preloader" style="display: none;"><td colspan="2" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                                </thead>
                                <tbody id="dashpeitemsacnthd">

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-1">

                    </div>
                    <div class="col-md-6">
                        <div class="columnchart chartstyle1" id="dialchartavtrate"></div>
                        <span id="avtestrateitem" class="estamtacnthd"></span><br>
                        <span id="avtactrateitem" class="estamtacnthd"></span>
                    </div>
                   
                </div>
                <div class="row show-grid">
                    <div class="col-md-4">
                        <div class="acnthdcolumnchart chartstyle" id="gaugechartacnthd"></div>
                       
                        <span id="estcons" class="estamtacnthd"></span><br>
                        <span id="actcons" class="estamtacnthd"></span>
                    </div>
                    <div class="col-md-4">
                        <div class="acnthdcolumnchart chartstyle1" id="gaugechartacnthdrate"></div>
                       
                        <span id="estrateitem" class="estamtacnthd"></span><br>
                        <span id="actrateitem" class="estamtacnthd"></span>
                    </div>
                    <div class="col-md-4">
                        <div class="acnthdcolumnchart chartstyle" id="gaugechartactres"></div>
                       
                        <span id="avtestcons" class="estamtacnthd"></span><br>
                        <span id="avtactcons" class="estamtacnthd"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>