/**
 * Created by SolmindsDelli5 on 26-09-2019.
 */
$(document).on( "click", "#financedashboard", function(){

    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }
    $('#findashboardsearch').trigger('click') ;
});
$(function(){
    $('#findashboardsearch').click(function(){
        //$('#accountspiesection').slideUp('slow');// slide down the project listing div

        //$('#findashboardsection').slideDown('slow');// slide down the project listing div

        var error=0;

        $('.error').hide();
        //$('#findashproject').val(36);
        if($('#findashproject').val()=='0')

        {

            $("#findashproject").next("span").html('Select Place').show('slow');

            $('#findashboardsection').slideUp('slow');

            error=1;

        }

        if(error==0)

        {

            //$('#findashboardsection').slideDown('slow');

            $.ajax({

                type: 'POST',

                url: '../FinanceRequests/ProjectCost',
                beforeSend : function(){

                    $('.preloader').show();

                },

                dataType: "json",

                data: {project:$('#findashproject').val()},

                success: function(data){

                    if(data.error=='No')

                    {
                        getProjectexpenditure();
                        getdialchart();
                        //getpebarchart();
                        getsubgrpdialchart();
                        $('.preloader').hide();
                        $('#dashpetable').show();
                        $('#dashpeincometable').show();

                        $('#dashpeitems').html(data.result);
                        $('#dashpeincomeitems').html(data.income);
                        $('#pedashinfo').html(data.peinfo);

                    }

                }

            });
        }

    });
});
function getProjectexpenditure()
{
    $.ajax({

        type: 'POST',

        url: '../FinanceRequests/Dashboard',

        dataType: "json",

        data: {project:$('#findashproject').val()},

        success: function(data){

            if(data.error=='No')

            {
                /*if(data.actualmargin<0){
                    $('#marginper').html('Margin: '+data.marginper+' %').show();
                }
                else {
                    $('#marginper').hide();
                }*/
                //alert(data.result)

                google.charts.load('current', {'packages':['corechart']});
                google.charts.setOnLoadCallback(drawChart);
                function drawChart() {

                    eval(data.result);

                    var options = {
                        //title: 'Statement of Project',
                        //is3D:false,
                        //legend: 'none',
                        //pieSliceText: 'percentage',
                        //pieSliceTextStyle: {color: 'white',fontSize:11},
                        //slices: {  9: {offset: 0.2}},
                        //}
                        //tooltip: { trigger: 'none' }
                        legend:'none',
                        isStacked: true,
                        animation: {startup:true,duration: 1000,easing: 'out'}
                    };
                    
                   /* var options = {
                	    tooltip: { trigger: 'none' },
                        pieSliceText: 'value',
                        //title: 'My Daily Activities',
                        sliceVisibilityThreshold: 0.00001,
                        legend: {
                            position: 'labeled'
                        }
                    };*/
                    
                   /* var options = {
                      //title: 'My Daily Activities',
                      pieHole: 0.4,
                    };*/

                    var chart = new google.visualization.BarChart(document.getElementById('piechart'));
                    chart.draw(data, options);
                }
            }
        }
    });
}

function getsubgrpdialchart()
{
    $.ajax({

        type: 'POST',

        url: '../FinanceRequests/Subgroupsdial',

        dataType: "json",

        data: {project:$('#findashproject').val()},

        success: function(data){

            if(data.error=='No')

            {
                $('.columnchart').hide();
                $('.estamt').hide();
                $('.actamt').hide();

                var greendata = data.greenrows;
                var datarr = greendata.split("|");
                var length=datarr.length;
                var eststrdata = data.estnumstrrows;
                var eststrdatarr = eststrdata.split("|");
                var actdata = data.actgrprows;
                var actdatarr = actdata.split("|");
                var actstrdata = data.actnumstrrows;
                var actstrdatarr = actstrdata.split("|");
                var actsbname = data.subgrpnamerows;
                var actsbdatarr = actsbname.split("|");
                var count = length -1;
                //var length=datarr.length;
                google.charts.load('current', {'packages':['gauge']});
                for (var k=0;k<=count;k++){
                    //var number=k+1;
                    google.charts.setOnLoadCallback(eval("drawChart"+k));

                }
                /*google.charts.setOnLoadCallback(drawChart);
                google.charts.setOnLoadCallback(drawChart1);
                google.charts.setOnLoadCallback(drawChart2);
                google.charts.setOnLoadCallback(drawChart3);
                google.charts.setOnLoadCallback(drawChart4);
                google.charts.setOnLoadCallback(drawChart5);
                google.charts.setOnLoadCallback(drawChart6);
                google.charts.setOnLoadCallback(drawChart7);
                google.charts.setOnLoadCallback(drawChart8);
                google.charts.setOnLoadCallback(drawChart9);
                google.charts.setOnLoadCallback(drawChart10);
                google.charts.setOnLoadCallback(drawChart11);*/
                //console.log(datarr[0])
                /*var actual = datarr[0]*1;
                var actual1 = datarr[1]*1;
                var actual2 = datarr[2]*1;
                var actual3 = datarr[3]*1;
                var actual4 = datarr[4]*1;
                var actual5 = datarr[5]*1;
                var actual6 = datarr[6]*1;
                var actual7 = datarr[7]*1;
                var actual8 = datarr[8]*1;
                var actual9 = datarr[9]*1;
                var actual10 = datarr[10]*1;
                var actual11 = datarr[11]*1;*/
                function drawChart0() {
                    var actual = actdatarr[0]*1;
                    var data = google.visualization.arrayToDataTable([
                        ['Label', 'Value'],
                        ['', actual]
                    ]);
                    //var estcost = datarr[0] / 4;
                    var estcost = (datarr[0] * 1) / 3;
                    var estcostinfo = Math.floor(datarr[0] * 100) / 100;
                    //console.log(estcost)
                    var majtricks = ['0'];
                    var numc=0;
                    for (var i=0;i<6;i++){
                        if (estcost < 1){
                            numc = numc + estcost;
                            majtricks.push(Math.floor(numc * 100) / 100)
                        }
                        else {
                            numc = numc + estcost;
                            majtricks.push(Math.round(numc))
                        }
                        //console.log(numc)
                        
                    }
                    //console.log(majtricks)
                    var options = {
                        //width: 400, height: 120,
                        min: 0, max: majtricks[6],
                        greenFrom: 0, greenTo: majtricks[3],
                        redFrom: majtricks[3], redTo: majtricks[6],
                        //yellowFrom:75, yellowTo: 90,
                        //majorTicks: ['0', '25', '50', '75', '100', '125', '150', '175', '200'],
                        majorTicks: majtricks,
                        minorTicks: 6
                    };
                    var formatoptions = new google.visualization.NumberFormat({
                        suffix: actstrdatarr[0],
                        fractionDigits: 2
                    });
                    formatoptions.format(data, 1);
                    $('#columnchart0').show();
                    $('#actsbgrpname0').show().html(actsbdatarr[0]);
                    $('#estamt0').show().html("E.amount: "+estcostinfo+" "+eststrdatarr[0]);
                    $('#actamt0').show().html("A.amount: "+actual+" "+actstrdatarr[0]);
                    var chart = new google.visualization.Gauge(document.getElementById('columnchart0'));
                    chart.draw(data, options);

                }
                function drawChart1() {
                    var actual1 = actdatarr[1]*1;
                    var data = google.visualization.arrayToDataTable([
                        ['Label', 'Value'],
                        ['', actual1]
                    ]);
                    //var estcost = datarr[1] / 4;
                    var estcost = (datarr[1] * 1) / 3;
                    var estcostinfo = Math.floor(datarr[1] * 100) / 100;
                    //console.log(estcost)
                    var majtricks = ['0'];
                    var numc=0;
                    for (var i=0;i<6;i++){
                        if (estcost < 1){
                            numc = numc + estcost;
                            majtricks.push(Math.floor(numc * 100) / 100)
                        }
                        else {
                            numc = numc + estcost;
                            majtricks.push(Math.round(numc))
                        }
                    }
                    var options = {
                        //width: 400, height: 120,
                        min: 0, max: majtricks[6],
                        greenFrom: 0, greenTo: majtricks[3],
                        redFrom: majtricks[3], redTo: majtricks[6],
                        //yellowFrom:75, yellowTo: 90,
                        //majorTicks: ['0', '25', '50', '75', '100', '125', '150', '175', '200'],
                        majorTicks: majtricks,
                        minorTicks: 6
                    };
                    var formatoptions = new google.visualization.NumberFormat({
                        suffix: actstrdatarr[1],
                        fractionDigits: 2
                    });
                    formatoptions.format(data, 1);
                    $('#columnchart1').show();
                    $('#actsbgrpname1').show().html(actsbdatarr[1]);
                    $('#estamt1').show().html("E.amount: "+estcostinfo+" "+eststrdatarr[1]);
                    $('#actamt1').show().html("A.amount: "+actual1+" "+actstrdatarr[1]);
                    var chart = new google.visualization.Gauge(document.getElementById('columnchart1'));
                    chart.draw(data, options);
                }
                function drawChart2() {
                    var actual2 = actdatarr[2]*1;
                    var data = google.visualization.arrayToDataTable([
                        ['Label', 'Value'],
                        ['', actual2]
                    ]);
                    //var estcost = datarr[2] / 4;
                    var estcost = (datarr[2] * 1) / 3;
                    var estcostinfo = Math.floor(datarr[2] * 100) / 100;
                    //console.log(estcost)
                    var majtricks = ['0'];
                    var numc=0;
                    for (var i=0;i<6;i++){
                        if (estcost < 1){
                            numc = numc + estcost;
                            majtricks.push(Math.floor(numc * 100) / 100)
                        }
                        else {
                            numc = numc + estcost;
                            majtricks.push(Math.round(numc))
                        }
                    }
                    //console.log(majtricks)
                    var options = {
                        //width: 400, height: 120,
                        min: 0, max: majtricks[6],
                        greenFrom: 0, greenTo: majtricks[3],
                        redFrom: majtricks[3], redTo: majtricks[6],
                        //yellowFrom:75, yellowTo: 90,
                        //majorTicks: ['0', '25', '50', '75', '100', '125', '150', '175', '200'],
                        majorTicks: majtricks,
                        minorTicks: 6
                    };
                    var formatoptions = new google.visualization.NumberFormat({
                        suffix: actstrdatarr[2],
                        fractionDigits: 2
                    });
                    formatoptions.format(data, 1);
                    $('#columnchart2').show();
                    $('#actsbgrpname2').show().html(actsbdatarr[2]);
                    $('#estamt2').show().html("E.amount: "+estcostinfo+" "+eststrdatarr[2]);
                    $('#actamt2').show().html("A.amount: "+actual2+" "+actstrdatarr[2]);
                    var chart = new google.visualization.Gauge(document.getElementById('columnchart2'));
                    chart.draw(data, options);
                }
                function drawChart3() {
                    var actual3 = actdatarr[3]*1;
                    var data = google.visualization.arrayToDataTable([
                        ['Label', 'Value'],
                        ['', actual3]
                    ]);
                    //var estcost = datarr[3] / 4;
                    var estcost = (datarr[3] * 1) / 3;
                    var estcostinfo = Math.floor(datarr[3] * 100) / 100;
                    //console.log(estcost)
                    var majtricks = ['0'];
                    var numc=0;
                    for (var i=0;i<6;i++){
                        if (estcost < 1){
                            numc = numc + estcost;
                            majtricks.push(Math.floor(numc * 100) / 100)
                        }
                        else {
                            numc = numc + estcost;
                            majtricks.push(Math.round(numc))
                        }
                    }
                    //console.log(majtricks)
                    var options = {
                        //width: 400, height: 120,
                        min: 0, max: majtricks[6],
                        greenFrom: 0, greenTo: majtricks[3],
                        redFrom: majtricks[3], redTo: majtricks[6],
                        //yellowFrom:75, yellowTo: 90,
                        //majorTicks: ['0', '25', '50', '75', '100', '125', '150', '175', '200'],
                        majorTicks: majtricks,
                        minorTicks: 6
                    };
                    var formatoptions = new google.visualization.NumberFormat({
                        suffix: actstrdatarr[3],
                        fractionDigits: 2
                    });
                    formatoptions.format(data, 1);
                    $('#columnchart3').show();
                    $('#actsbgrpname3').show().html(actsbdatarr[3]);
                    $('#estamt3').show().html("E.amount: "+estcostinfo+" "+eststrdatarr[3]);
                    $('#actamt3').show().html("A.amount: "+actual3+" "+actstrdatarr[3]);
                    var chart = new google.visualization.Gauge(document.getElementById('columnchart3'));
                    chart.draw(data, options);
                }
                function drawChart4() {
                    var actual4 = actdatarr[4]*1;
                    var data = google.visualization.arrayToDataTable([
                        ['Label', 'Value'],
                        ['', actual4]
                    ]);
                    //var estcost = datarr[4] / 4;
                    var estcost = (datarr[4] * 1) / 3;
                    var estcostinfo = Math.floor(datarr[4] * 100) / 100;
                    //console.log(estcost)
                    var majtricks = ['0'];
                    var numc=0;
                    for (var i=0;i<6;i++){
                        if (estcost < 1){
                            numc = numc + estcost;
                            majtricks.push(Math.floor(numc * 100) / 100)
                        }
                        else {
                            numc = numc + estcost;
                            majtricks.push(Math.round(numc))
                        }
                    }
                    //console.log(majtricks)
                    var options = {
                        //width: 400, height: 120,
                        min: 0, max: majtricks[6],
                        greenFrom: 0, greenTo: majtricks[3],
                        redFrom: majtricks[3], redTo: majtricks[6],
                        //yellowFrom:75, yellowTo: 90,
                        //majorTicks: ['0', '25', '50', '75', '100', '125', '150', '175', '200'],
                        majorTicks: majtricks,
                        minorTicks: 6
                    };
                    var formatoptions = new google.visualization.NumberFormat({
                        suffix: actstrdatarr[4],
                        fractionDigits: 2
                    });
                    formatoptions.format(data, 1);
                    $('#columnchart4').show();
                    $('#actsbgrpname4').show().html(actsbdatarr[4]);
                    $('#estamt4').show().html("E.amount: "+estcostinfo+" "+eststrdatarr[4]);
                    $('#actamt4').show().html("A.amount: "+actual4+" "+actstrdatarr[4]);
                    var chart = new google.visualization.Gauge(document.getElementById('columnchart4'));
                    chart.draw(data, options);
                }
                function drawChart5() {
                    var actual5 = actdatarr[5]*1;
                    var data = google.visualization.arrayToDataTable([
                        ['Label', 'Value'],
                        ['', actual5]
                    ]);
                    //var estcost = datarr[5] / 4;
                    var estcost = (datarr[5] * 1) / 3;
                    var estcostinfo = Math.floor(datarr[5] * 100) / 100;
                    //console.log(estcost)
                    var majtricks = ['0'];
                    var numc=0;
                    for (var i=0;i<6;i++){
                        if (estcost < 1){
                            numc = numc + estcost;
                            majtricks.push(Math.floor(numc * 100) / 100)
                        }
                        else {
                            numc = numc + estcost;
                            majtricks.push(Math.round(numc))
                        }
                    }
                    //console.log(majtricks)
                    var options = {
                        //width: 400, height: 120,
                        min: 0, max: majtricks[6],
                        greenFrom: 0, greenTo: majtricks[3],
                        redFrom: majtricks[3], redTo: majtricks[6],
                        //yellowFrom:75, yellowTo: 90,
                        //majorTicks: ['0', '25', '50', '75', '100', '125', '150', '175', '200'],
                        majorTicks: majtricks,
                        minorTicks: 6
                    };
                    var formatoptions = new google.visualization.NumberFormat({
                        suffix: actstrdatarr[5],
                        fractionDigits: 2
                    });
                    formatoptions.format(data, 1);
                    $('#columnchart5').show();
                    $('#actsbgrpname5').show().html(actsbdatarr[5]);
                    $('#estamt5').show().html("E.amount: "+estcostinfo+" "+eststrdatarr[5]);
                    $('#actamt5').show().html("A.amount: "+actual5+" "+actstrdatarr[5]);
                    var chart = new google.visualization.Gauge(document.getElementById('columnchart5'));
                    chart.draw(data, options);
                }
                function drawChart6() {
                    var actual6 = actdatarr[6]*1;
                    var data = google.visualization.arrayToDataTable([
                        ['Label', 'Value'],
                        ['', actual6]
                    ]);
                    //var estcost = datarr[6] / 4;
                    var estcost = (datarr[6] * 1) / 3;
                    var estcostinfo = Math.floor(datarr[6] * 100) / 100;
                    //console.log(estcost)
                    var majtricks = ['0'];
                    var numc=0;
                    for (var i=0;i<6;i++){
                        if (estcost < 1){
                            numc = numc + estcost;
                            majtricks.push(Math.floor(numc * 100) / 100)
                        }
                        else {
                            numc = numc + estcost;
                            majtricks.push(Math.round(numc))
                        }
                    }
                    //console.log(majtricks)
                    var options = {
                        //width: 400, height: 120,
                        min: 0, max: majtricks[6],
                        greenFrom: 0, greenTo: majtricks[3],
                        redFrom: majtricks[3], redTo: majtricks[6],
                        //yellowFrom:75, yellowTo: 90,
                        //majorTicks: ['0', '25', '50', '75', '100', '125', '150', '175', '200'],
                        majorTicks: majtricks,
                        minorTicks: 6
                    };
                    var formatoptions = new google.visualization.NumberFormat({
                        suffix: actstrdatarr[6],
                        fractionDigits: 2
                    });
                    formatoptions.format(data, 1);
                    $('#columnchart6').show();
                    $('#actsbgrpname6').show().html(actsbdatarr[6]);
                    $('#estamt6').show().html("E.amount: "+estcostinfo+" "+eststrdatarr[6]);
                    $('#actamt6').show().html("A.amount: "+actual6+" "+actstrdatarr[6]);
                    var chart = new google.visualization.Gauge(document.getElementById('columnchart6'));
                    chart.draw(data, options);
                }
                function drawChart7() {
                    var actual7 = actdatarr[7]*1;
                    var data = google.visualization.arrayToDataTable([
                        ['Label', 'Value'],
                        ['', actual7]
                    ]);
                    //var estcost = datarr[7] / 4;
                    var estcost = (datarr[7] * 1) / 3;
                    var estcostinfo = Math.floor(datarr[7] * 100) / 100;
                    //console.log(estcost)
                    var majtricks = ['0'];
                    var numc=0;
                    for (var i=0;i<6;i++){
                        if (estcost < 1){
                            numc = numc + estcost;
                            majtricks.push(Math.floor(numc * 100) / 100)
                        }
                        else {
                            numc = numc + estcost;
                            majtricks.push(Math.round(numc))
                        }
                    }
                    //console.log(majtricks)
                    var options = {
                        //width: 400, height: 120,
                        min: 0, max: majtricks[6],
                        greenFrom: 0, greenTo: majtricks[3],
                        redFrom: majtricks[3], redTo: majtricks[6],
                        //yellowFrom:75, yellowTo: 90,
                        //majorTicks: ['0', '25', '50', '75', '100', '125', '150', '175', '200'],
                        majorTicks: majtricks,
                        minorTicks: 6
                    };
                    var formatoptions = new google.visualization.NumberFormat({
                        suffix: actstrdatarr[7],
                        fractionDigits: 2
                    });
                    formatoptions.format(data, 1);
                    $('#columnchart7').show();
                    $('#actsbgrpname7').show().html(actsbdatarr[7]);
                    $('#estamt7').show().html("E.amount: "+estcostinfo+" "+eststrdatarr[7]);
                    $('#actamt7').show().html("A.amount: "+actual7+" "+actstrdatarr[7]);
                    var chart = new google.visualization.Gauge(document.getElementById('columnchart7'));
                    chart.draw(data, options);

                }
                function drawChart8() {
                    var actual8 = actdatarr[8]*1;
                    var data = google.visualization.arrayToDataTable([
                        ['Label', 'Value'],
                        ['', actual8]
                    ]);
                    //var estcost = datarr[8] / 4;
                    var estcost = (datarr[8] * 1) / 3;
                    var estcostinfo = Math.floor(datarr[8] * 100) / 100;
                    //console.log(estcost)
                    var majtricks = ['0'];
                    var numc=0;
                    for (var i=0;i<6;i++){
                        if (estcost < 1){
                            numc = numc + estcost;
                            majtricks.push(Math.floor(numc * 100) / 100)
                        }
                        else {
                            numc = numc + estcost;
                            majtricks.push(Math.round(numc))
                        }
                    }
                    //console.log(majtricks)
                    var options = {
                        //width: 400, height: 120,
                        min: 0, max: majtricks[6],
                        greenFrom: 0, greenTo: majtricks[3],
                        redFrom: majtricks[3], redTo: majtricks[6],
                        //yellowFrom:75, yellowTo: 90,
                        //majorTicks: ['0', '25', '50', '75', '100', '125', '150', '175', '200'],
                        majorTicks: majtricks,
                        minorTicks: 6
                    };
                    var formatoptions = new google.visualization.NumberFormat({
                        suffix: actstrdatarr[8],
                        fractionDigits: 2
                    });
                    formatoptions.format(data, 1);
                    $('#columnchart8').show();
                    $('#actsbgrpname8').show().html(actsbdatarr[8]);
                    $('#estamt8').show().html("E.amount: "+estcostinfo+" "+eststrdatarr[8]);
                    $('#actamt8').show().html("A.amount: "+actual8+" "+actstrdatarr[8]);
                    var chart = new google.visualization.Gauge(document.getElementById('columnchart8'));
                    chart.draw(data, options);

                }
                function drawChart9() {
                    var actual9 = actdatarr[9]*1;
                    var data = google.visualization.arrayToDataTable([
                        ['Label', 'Value'],
                        ['', actual9]
                    ]);
                    //var estcost = datarr[9] / 4;
                    var estcost = (datarr[9] * 1) / 3;
                    var estcostinfo = Math.floor(datarr[9] * 100) / 100;
                    //console.log(estcost)
                    var majtricks = ['0'];
                    var numc=0;
                    for (var i=0;i<6;i++){
                        if (estcost < 1){
                            numc = numc + estcost;
                            majtricks.push(Math.floor(numc * 100) / 100)
                        }
                        else {
                            numc = numc + estcost;
                            majtricks.push(Math.round(numc))
                        }
                    }
                    //console.log(majtricks)
                    var options = {
                        //width: 400, height: 120,
                        min: 0, max: majtricks[6],
                        greenFrom: 0, greenTo: majtricks[3],
                        redFrom: majtricks[3], redTo: majtricks[6],
                        //yellowFrom:75, yellowTo: 90,
                        //majorTicks: ['0', '25', '50', '75', '100', '125', '150', '175', '200'],
                        majorTicks: majtricks,
                        minorTicks: 6
                    };
                    var formatoptions = new google.visualization.NumberFormat({
                        suffix: actstrdatarr[9],
                        fractionDigits: 2
                    });
                    formatoptions.format(data, 1);
                    $('#columnchart9').show();
                    $('#actsbgrpname9').show().html(actsbdatarr[9]);
                    $('#estamt9').show().html("E.amount: "+estcostinfo+" "+eststrdatarr[9]);
                    $('#actamt9').show().html("A.amount: "+actual9+" "+actstrdatarr[9]);
                    var chart = new google.visualization.Gauge(document.getElementById('columnchart9'));
                    chart.draw(data, options);

                }
                function drawChart10() {
                    var actual10 = actdatarr[10]*1;
                    var data = google.visualization.arrayToDataTable([
                        ['Label', 'Value'],
                        ['', actual10]
                    ]);
                    //var estcost = datarr[10] / 4;
                    var estcost = (datarr[10] * 1) / 3;
                    var estcostinfo = Math.floor(datarr[10] * 100) / 100;
                    //console.log(estcost)
                    var majtricks = ['0'];
                    var numc=0;
                    for (var i=0;i<6;i++){
                        if (estcost < 1){
                            numc = numc + estcost;
                            majtricks.push(Math.floor(numc * 100) / 100)
                        }
                        else {
                            numc = numc + estcost;
                            majtricks.push(Math.round(numc))
                        }
                    }
                    //console.log(majtricks)
                    var options = {
                        //width: 400, height: 120,
                        min: 0, max: majtricks[6],
                        greenFrom: 0, greenTo: majtricks[3],
                        redFrom: majtricks[3], redTo: majtricks[6],
                        //yellowFrom:75, yellowTo: 90,
                        //majorTicks: ['0', '25', '50', '75', '100', '125', '150', '175', '200'],
                        majorTicks: majtricks,
                        minorTicks: 6
                    };
                    var formatoptions = new google.visualization.NumberFormat({
                        suffix: actstrdatarr[10],
                        fractionDigits: 2
                    });
                    formatoptions.format(data, 1);
                    $('#columnchart10').show();
                    $('#actsbgrpname10').show().html(actsbdatarr[10]);
                    $('#estamt10').show().html("E.amount: "+estcostinfo+" "+eststrdatarr[10]);
                    $('#actamt10').show().html("A.amount: "+actual10+" "+actstrdatarr[10]);
                    var chart = new google.visualization.Gauge(document.getElementById('columnchart10'));
                    chart.draw(data, options);

                }
                function drawChart11() {
                    var actual11 = actdatarr[11]*1;
                    var data = google.visualization.arrayToDataTable([
                        ['Label', 'Value'],
                        ['', actual11]
                    ]);
                    //var estcost = datarr[11] / 4;
                    var estcost = (datarr[11] * 1) / 3;
                    var estcostinfo = Math.floor(datarr[11] * 100) / 100;
                    //console.log(estcost)
                    var majtricks = ['0'];
                    var numc=0;
                    for (var i=0;i<6;i++){
                        if (estcost < 1){
                            numc = numc + estcost;
                            majtricks.push(Math.floor(numc * 100) / 100)
                        }
                        else {
                            numc = numc + estcost;
                            majtricks.push(Math.round(numc))
                        }
                    }
                    //console.log(majtricks)
                    var options = {
                        //width: 400, height: 120,
                        min: 0, max: majtricks[6],
                        greenFrom: 0, greenTo: majtricks[3],
                        redFrom: majtricks[3], redTo: majtricks[6],
                        //yellowFrom:75, yellowTo: 90,
                        //majorTicks: ['0', '25', '50', '75', '100', '125', '150', '175', '200'],
                        majorTicks: majtricks,
                        minorTicks: 6
                    };
                    var formatoptions = new google.visualization.NumberFormat({
                        suffix: actstrdatarr[11],
                        fractionDigits: 2
                    });
                    formatoptions.format(data, 1);
                    $('#columnchart11').show();
                    $('#actsbgrpname11').show().html(actsbdatarr[11]);
                    $('#estamt11').show().html("E.amount: "+estcostinfo+" "+eststrdatarr[11]);
                    $('#actamt11').show().html("A.amount: "+actual11+" "+actstrdatarr[11]);
                    var chart = new google.visualization.Gauge(document.getElementById('columnchart11'));
                    chart.draw(data, options);

                }

                    /*google.charts.load('current', {
                        packages: ['gauge']
                    }).then(function () {
                        for (var i=0;i<length;i++) {
                            //console.log(testdatarr[i])
                            var dataHumid = google.visualization.arrayToDataTable([
                                ['Label', 'Value'],
                                ['', length]
                                //['',length]
                            ]);

                            var optionsHumid = {
                                min: 0, max: 200,
                                greenFrom: 0, greenTo: datarr[i],
                                //yellowFrom: datarr[i], yellowTo: 100,
                                redFrom: 100, redTo: 200,
                                majorTicks: ['0', '25', '50', '75', '100', '125', '150', '175', '200'],
                                minorTicks: 5
                            };

                            var formatHumid = new google.visualization.NumberFormat({
                                suffix: ' Cr',
                                fractionDigits: 0
                            });
                            formatHumid.format(dataHumid, 1);
                            $('#columnchart' + i).show();
                            var chartHumid = new google.visualization.Gauge(document.getElementById("columnchart"+i));

                            chartHumid.draw(dataHumid, optionsHumid);

                        }

                    });*/


            }

        }

    });
}

function getdialchart()
{
    $.ajax({

        type: 'POST',

        url: '../FinanceRequests/Dialgaugecal',
        beforeSend : function(){

            $('.preloader').show();

        },

        dataType: "json",

        data: {project:$('#findashproject').val()},

        success: function(data){

            if(data.error=='No')

            {
                //$('.estamt').hide();
                //$('.actamt').hide();
                var actualamt=data.actual;
                var estcostamt=data.estimate;
                var actualstr=data.actualstr;

                google.charts.load('current', {'packages':['gauge']});
                google.charts.setOnLoadCallback(eval("drawChart"));
                
                function drawChart() {
                    var actual = actualamt * 1;
                    var data = google.visualization.arrayToDataTable([
                        ['Label', 'Value'],
                        ['', actual]
                    ]);
                    //var estcost = estcostamt / 4;
                    var estcost = (estcostamt * 1) / 3;
                    //console.log(estcost)
                    var majtricks = ['0'];
                    var numc=0;
                    for (var i=0;i<6;i++){
                        if (estcost < 1){
                            numc = numc + estcost;
                            majtricks.push(Math.floor(numc * 100) / 100)
                        }
                        else {
                            numc = numc + estcost;
                            majtricks.push(Math.round(numc))
                        }
                    }
                    //console.log(majtricks)
                    var options = {
                        //width: 400, height: 120,
                        min: 0, max: majtricks[6],
                        greenFrom: 0, greenTo: majtricks[3],
                        redFrom: majtricks[3], redTo: majtricks[6],
                        //yellowFrom:75, yellowTo: 90,
                        //majorTicks: ['0', '25', '50', '75', '100', '125', '150', '175', '200'],
                        majorTicks: majtricks,
                        minorTicks: 6
                    };
                    var formatoptions = new google.visualization.NumberFormat({
                        suffix: actualstr,
                        fractionDigits: 2
                    });
                    formatoptions.format(data, 1);
                    //$('#estamt').show().html("E.amount: "+majtricks[4]+" "+eststrdatarr[0]);
                    //$('#actamt').show().html("A.amount: "+actual+" "+actstrdatarr[0]);
                    var chart = new google.visualization.Gauge(document.getElementById('gauge_div'));
                    chart.draw(data, options);

                }
               
            }

        }

    });
}

function getpebarchart()
{
    $.ajax({

        type: 'POST',

        url: '../FinanceRequests/Monthwisepe',

        dataType: "json",

        data: {project:$('#findashproject').val()},

        success: function(data){

            if(data.error=='No')

            {
                $('.columnchart').hide();
                //google.charts.load('current', {'packages':['bar']});
                google.charts.load("current", {packages:['corechart']});
                var result=data.result;
                var datarr = result.split("|");
                var length=datarr.length;

                //console.log(datarr);
                var subgrps=data.subgrprows;
                var name = subgrps.split("|");

                for (var i=0;i<length;i++)
                {
                    var number=i+1;
                    google.charts.setOnLoadCallback(eval("drawChart"+number));
                }
                function drawChart1() {
                    eval(datarr[0]);
                    var max = subgrparr0.getColumnRange(5).max;
                    if(max < 5){
                       var range = 10;
                    }
                    else if(max < 10 && max > 5){
                       var range = 20;
                    }
                    else if(max < 20 && max > 10){
                       var range = 30;
                    }
                    else if(max < 50 && max > 20){
                       var range = 60;
                    }
                    else if(max < 90 && max > 50){
                       var range = 100;
                    }  
                    else if(max < 100 && max > 90){
                       var range = 125;
                    } 
                    else if(max < 125 && max > 100){
                       var range = 150;
                    } 
                    else{
                       var range = Math.round(max) + 10;
                    }
                       
                    //var range = Math.round(max1) + 10;
                    var options = {title: name[0]+' (in months)',legend:'none',isStacked: true,animation: {startup:true,duration: 1000,easing: 'out'},annotations:{alwaysOutside:true,textStyle:{bold: true,color: '#ffffff'}},vAxis: {
                        viewWindow: {
                            max: range
                        },
                    }};
                    $('#columnchart0').show();
                    var chart = new google.visualization.ColumnChart(document.getElementById("columnchart0"));
                    chart.draw(subgrparr0, options);
                }
                function drawChart2() {
                    eval(datarr[1]);
                    var max = subgrparr1.getColumnRange(5).max;
                    if(max < 5){
                       var range = 10;
                    }
                    else if(max < 10 && max > 5){
                       var range = 20;
                    }
                    else if(max < 20 && max > 10){
                       var range = 30;
                    }
                    else if(max < 50 && max > 20){
                       var range = 60;
                    }
                    else if(max < 90 && max > 50){
                       var range = 100;
                    }  
                    else if(max < 100 && max > 90){
                       var range = 125;
                    } 
                    else if(max < 125 && max > 100){
                       var range = 150;
                    }
                    else{
                       var range = Math.round(max) + 10;
                    }
                    
                    var options = {title: name[1]+' (in months)',legend:'none',isStacked: true,animation: {startup:true,duration: 1000,easing: 'out'},annotations:{alwaysOutside:true,textStyle:{bold: true,color: '#ffffff'}},vAxis: {
                        viewWindow: {
                            max: range
                        },
                    }};
                    $('#columnchart1').show();
                    var chart = new google.visualization.ColumnChart(document.getElementById("columnchart1"));
                    chart.draw(subgrparr1, options);
                }
                function drawChart3() {
                    eval(datarr[2]);
                    var max = subgrparr2.getColumnRange(5).max;
                    if(max < 5){
                       var range = 10;
                    }
                    else if(max < 10 && max > 5){
                       var range = 20;
                    }
                    else if(max < 20 && max > 10){
                       var range = 30;
                    }
                    else if(max < 50 && max > 20){
                       var range = 60;
                    }
                    else if(max < 90 && max > 50){
                       var range = 100;
                    }  
                    else if(max < 100 && max > 90){
                       var range = 125;
                    } 
                    else if(max < 125 && max > 100){
                       var range = 150;
                    }
                    else{
                       var range = Math.round(max) + 10;
                    }
                    
                    var options = {title: name[2]+' (in months)',legend:'none',isStacked: true,animation: {startup:true,duration: 1000,easing: 'out'},annotations:{alwaysOutside:true,textStyle:{bold: true,color: '#ffffff'}},vAxis: {
                        viewWindow: {
                            max: range
                        },
                    }};
                    $('#columnchart2').show();
                    var chart = new google.visualization.ColumnChart(document.getElementById("columnchart2"));
                    chart.draw(subgrparr2, options);
                }
                function drawChart4() {
                    eval(datarr[3]);
                    var max = subgrparr3.getColumnRange(5).max;
                    if(max < 5){
                       var range = 10;
                    }
                    else if(max < 10 && max > 5){
                       var range = 20;
                    }
                    else if(max < 20 && max > 10){
                       var range = 30;
                    }
                    else if(max < 50 && max > 20){
                       var range = 60;
                    }
                    else if(max < 90 && max > 50){
                       var range = 100;
                    }  
                    else if(max < 100 && max > 90){
                       var range = 125;
                    } 
                    else if(max < 125 && max > 100){
                       var range = 150;
                    }
                    else{
                       var range = Math.round(max) + 10;
                    }
                    
                    var options = {title: name[3]+' (in months)',legend:'none',isStacked: true,animation: {startup:true,duration: 1000,easing: 'out'},annotations:{alwaysOutside:true,textStyle:{bold: true,color: '#ffffff'}},vAxis: {
                        viewWindow: {
                            max: range
                        },
                    }};
                    $('#columnchart3').show();
                    var chart = new google.visualization.ColumnChart(document.getElementById("columnchart3"));
                    chart.draw(subgrparr3, options);
                }
                function drawChart5() {
                    eval(datarr[4]);
                    var max = subgrparr4.getColumnRange(5).max;
                    if(max < 5){
                       var range = 10;
                    }
                    else if(max < 10 && max > 5){
                       var range = 20;
                    }
                    else if(max < 20 && max > 10){
                       var range = 30;
                    }
                    else if(max < 50 && max > 20){
                       var range = 60;
                    }
                    else if(max < 90 && max > 50){
                       var range = 100;
                    }  
                    else if(max < 100 && max > 90){
                       var range = 125;
                    } 
                    else if(max < 125 && max > 100){
                       var range = 150;
                    }
                    else{
                       var range = Math.round(max) + 10;
                    }
                    
                    var options = {title: name[4]+' (in months)',legend:'none',isStacked: true,animation: {startup:true,duration: 1000,easing: 'out'},annotations:{alwaysOutside:true,textStyle:{bold: true,color: '#ffffff'}},vAxis: {
                        viewWindow: {
                            max: range
                        },
                    }};
                    $('#columnchart4').show();
                    var chart = new google.visualization.ColumnChart(document.getElementById("columnchart4"));
                    chart.draw(subgrparr4, options);
                }
                function drawChart6() {
                    eval(datarr[5]);
                    var max = subgrparr5.getColumnRange(5).max;
                    if(max < 5){
                       var range = 10;
                    }
                    else if(max < 10 && max > 5){
                       var range = 20;
                    }
                    else if(max < 20 && max > 10){
                       var range = 30;
                    }
                    else if(max < 50 && max > 20){
                       var range = 60;
                    }
                    else if(max < 90 && max > 50){
                       var range = 100;
                    }  
                    else if(max < 100 && max > 90){
                       var range = 125;
                    } 
                    else if(max < 125 && max > 100){
                       var range = 150;
                    }
                    else{
                       var range = Math.round(max) + 10;
                    }
                    
                    var options = {title: name[5]+' (in months)',legend:'none',isStacked: true,animation: {startup:true,duration: 1000,easing: 'out'},annotations:{alwaysOutside:true,textStyle:{bold: true,color: '#ffffff'}},vAxis: {
                        viewWindow: {
                            max: range
                        },
                    }};
                    $('#columnchart5').show();
                    var chart = new google.visualization.ColumnChart(document.getElementById("columnchart5"));
                    chart.draw(subgrparr5, options);
                }
                function drawChart7() {
                    eval(datarr[6]);
                    var max = subgrparr6.getColumnRange(5).max;
                    if(max < 5){
                       var range = 10;
                    }
                    else if(max < 10 && max > 5){
                       var range = 20;
                    }
                    else if(max < 20 && max > 10){
                       var range = 30;
                    }
                    else if(max < 50 && max > 20){
                       var range = 60;
                    }
                    else if(max < 90 && max > 50){
                       var range = 100;
                    }  
                    else if(max < 100 && max > 90){
                       var range = 125;
                    } 
                    else if(max < 125 && max > 100){
                       var range = 150;
                    } 
                    else{
                       var range = Math.round(max) + 10;
                    }
                    
                    var options = {title: name[6]+ ' (in months)',legend:'none',isStacked: true,animation: {startup:true,duration: 1000,easing: 'out'},annotations:{alwaysOutside:true,textStyle:{bold: true,color: '#ffffff'}},vAxis: {
                        viewWindow: {
                            max: range
                        },
                    }};
                    $('#columnchart6').show();
                    var chart = new google.visualization.ColumnChart(document.getElementById("columnchart6"));
                    chart.draw(subgrparr6, options);
                }
                function drawChart8() {
                    eval(datarr[7]);
                    var max = subgrparr7.getColumnRange(5).max;
                    if(max < 5){
                       var range = 10;
                    }
                    else if(max < 10 && max > 5){
                       var range = 20;
                    }
                    else if(max < 20 && max > 10){
                       var range = 30;
                    }
                    else if(max < 50 && max > 20){
                       var range = 60;
                    }
                    else if(max < 90 && max > 50){
                       var range = 100;
                    }  
                    else if(max < 100 && max > 90){
                       var range = 125;
                    } 
                    else if(max < 125 && max > 100){
                       var range = 150;
                    }
                    else{
                       var range = Math.round(max) + 10;
                    }
                    
                    var options = {title: name[7]+' (in months)',legend:'none',isStacked: true,animation: {startup:true,duration: 1000,easing: 'out'},annotations:{alwaysOutside:true,textStyle:{bold: true,color: '#ffffff'}},vAxis: {
                        viewWindow: {
                            max: range
                        },
                    }};
                    $('#columnchart7').show();
                    var chart = new google.visualization.ColumnChart(document.getElementById("columnchart7"));
                    chart.draw(subgrparr7, options);
                }
                function drawChart9() {
                    eval(datarr[8]);
                    var max = subgrparr8.getColumnRange(5).max;
                    if(max < 5){
                       var range = 10;
                    }
                    else if(max < 10 && max > 5){
                       var range = 20;
                    }
                    else if(max < 20 && max > 10){
                       var range = 30;
                    }
                    else if(max < 50 && max > 20){
                       var range = 60;
                    }
                    else if(max < 90 && max > 50){
                       var range = 100;
                    }  
                    else if(max < 100 && max > 90){
                       var range = 125;
                    } 
                    else if(max < 125 && max > 100){
                       var range = 150;
                    }
                    else{
                       var range = Math.round(max) + 10;
                    }
                    
                    var options = {title: name[8]+' (in months)',legend:'none',isStacked: true,animation: {startup:true,duration: 1000,easing: 'out'},annotations:{alwaysOutside:true,textStyle:{bold: true,color: '#ffffff'}},vAxis: {
                        viewWindow: {
                            max: range
                        },
                    }};
                    $('#columnchart8').show();
                    var chart = new google.visualization.ColumnChart(document.getElementById("columnchart8"));
                    chart.draw(subgrparr8, options);
                }
                function drawChart10() {
                    eval(datarr[9]);
                    var max = subgrparr9.getColumnRange(5).max;
                    if(max < 5){
                       var range = 10;
                    }
                    else if(max < 10 && max > 5){
                       var range = 20;
                    }
                    else if(max < 20 && max > 10){
                       var range = 30;
                    }
                    else if(max < 50 && max > 20){
                       var range = 60;
                    }
                    else if(max < 90 && max > 50){
                       var range = 100;
                    }  
                    else if(max < 100 && max > 90){
                       var range = 125;
                    } 
                    else if(max < 125 && max > 100){
                       var range = 150;
                    }
                    else{
                       var range = Math.round(max) + 10;
                    }
                    
                    //var options = {title: name[9]+' Expense (in months)',legend:'bottom',colors: ['#61d836']};
                    var options = {title: name[9]+' (in months)',legend:'none',isStacked: true,animation: {startup:true,duration: 1000,easing: 'out'},annotations:{alwaysOutside:true,textStyle:{bold: true,color: '#ffffff'}},vAxis: {
                        viewWindow: {
                            max: range
                        },
                    }};
                    $('#columnchart9').show();
                    var chart = new google.visualization.ColumnChart(document.getElementById("columnchart9"));
                    chart.draw(subgrparr9, options);
                }
                function drawChart11() {
                    eval(datarr[10]);
                    var max = subgrparr10.getColumnRange(5).max;
                    if(max < 5){
                        var range = 10;
                    }
                    else if(max < 10 && max > 5){
                        var range = 20;
                    }
                    else if(max < 20 && max > 10){
                        var range = 30;
                    }
                    else if(max < 50 && max > 20){
                        var range = 60;
                    }
                    else if(max < 90 && max > 50){
                        var range = 100;
                    }
                    else if(max < 100 && max > 90){
                        var range = 125;
                    }
                    else if(max < 125 && max > 100){
                        var range = 150;
                    }
                    else{
                        var range = Math.round(max) + 10;
                    }

                    //var options = {title: name[9]+' Expense (in months)',legend:'bottom',colors: ['#61d836']};
                    var options = {title: name[10]+' (in months)',legend:'none',isStacked: true,animation: {startup:true,duration: 1000,easing: 'out'},annotations:{alwaysOutside:true,textStyle:{bold: true,color: '#ffffff'}},vAxis: {
                        viewWindow: {
                            max: range
                        },
                    }};
                    $('#columnchart10').show();
                    var chart = new google.visualization.ColumnChart(document.getElementById("columnchart10"));
                    chart.draw(subgrparr10, options);
                }
                function drawChart12() {
                    eval(datarr[11]);
                    var max = subgrparr11.getColumnRange(5).max;
                    if(max < 5){
                        var range = 10;
                    }
                    else if(max < 10 && max > 5){
                        var range = 20;
                    }
                    else if(max < 20 && max > 10){
                        var range = 30;
                    }
                    else if(max < 50 && max > 20){
                        var range = 60;
                    }
                    else if(max < 90 && max > 50){
                        var range = 100;
                    }
                    else if(max < 100 && max > 90){
                        var range = 125;
                    }
                    else if(max < 125 && max > 100){
                        var range = 150;
                    }
                    else{
                        var range = Math.round(max) + 10;
                    }

                    //var options = {title: name[9]+' Expense (in months)',legend:'bottom',colors: ['#61d836']};
                    var options = {title: name[11]+' (in months)',legend:'none',isStacked: true,animation: {startup:true,duration: 1000,easing: 'out'},annotations:{alwaysOutside:true,textStyle:{bold: true,color: '#ffffff'}},vAxis: {
                        viewWindow: {
                            max: range
                        },
                    }};
                    $('#columnchart11').show();
                    var chart = new google.visualization.ColumnChart(document.getElementById("columnchart11"));
                    chart.draw(subgrparr11, options);
                }
            }
        }
    });

}
$(document).on('click','.expensesub',function(){

    //$('#findashboardsection').slideUp('slow');// slide down the project listing div

    //$('#accountspiesection').slideDown('slow');// slide down the project listing div
    $('#findashboardsection').hide('slide', {direction: 'left'}, 1000);
    $('#accountsgaugesection').hide('slide', {direction: 'left'}, 1000);
    $('#accountspiesection').show('slide', {direction: 'right'}, 1000);
    //$('#accountspiesection').show();
    var id=$(this).attr('data-id');
    var subgrpid=$('#subgrpid'+id).val();
    $.ajax({

        type: 'POST',

        url: '../FinanceRequests/AccountheadCost',
        beforeSend : function(){

            $('.preloader').show();

        },

        dataType: "json",

        data: {subgrpid:subgrpid,project:$('#findashproject').val()},

        success: function(data){

            if(data.error=='No')

            {
                //getaccountsbarchart(subgrpid);
                getacntheaddialchart(subgrpid)
                $('.preloader').hide();
                $('#dashpeacnthdtable').show();
                $('#piechartacnthd').show();

                $('#dashpeacnthditems').html(data.result);
                $('#acntgrpdashinfo').html(data.subgroupinfo);

                var actualamt=data.actualamt;
                var estcostamt=data.estimateamt;
                var actualstr=data.actualamtstr;
                //console.log('estcostamt: '+estcostamt)
                //console.log('actualamt: '+actualamt)
                google.charts.load('current', {'packages':['gauge']});
                google.charts.setOnLoadCallback(eval("drawChart"));
                
                function drawChart() {
                    var actual = actualamt * 1;
                    var data = google.visualization.arrayToDataTable([
                        ['Label', 'Value'],
                        ['', actual]
                    ]);
                    //var estcost = estcostamt / 4;
                    var estcost = (estcostamt * 1) / 3;
                    //console.log(estcost)
                    var majtricks = ['0'];
                    var numc=0;
                    for (var i=0;i<6;i++){
                        
                        if (estcost < 1){
                            numc = numc + estcost;
                            majtricks.push(Math.floor(numc * 100) / 100)
                        }
                        else {
                            numc = numc + estcost;
                            majtricks.push(Math.round(numc))
                        }
                    }
                    //console.log(majtricks)
                    var options = {
                        //width: 400, height: 120,
                        min: 0, max: majtricks[6],
                        greenFrom: 0, greenTo: majtricks[3],
                        redFrom: majtricks[3], redTo: majtricks[6],
                        //yellowFrom:75, yellowTo: 90,
                        //majorTicks: ['0', '25', '50', '75', '100', '125', '150', '175', '200'],
                        majorTicks: majtricks,
                        minorTicks: 6
                    };
                    var formatoptions = new google.visualization.NumberFormat({
                        suffix: actualstr,
                        fractionDigits: 2
                    });
                    formatoptions.format(data, 1);
                    //$('#estamt').show().html("E.amount: "+majtricks[4]+" "+eststrdatarr[0]);
                    //$('#actamt').show().html("A.amount: "+actual+" "+actstrdatarr[0]);
                    var chart = new google.visualization.Gauge(document.getElementById('piechartacnthd'));
                    chart.draw(data, options);

                }

            }

        }

    });
});

$(document).on('click','.expensesubacnthd',function(){
    $('#findashboardsection').hide('slide', {direction: 'left'}, 1000);
    $('#accountspiesection').hide('slide', {direction: 'right'}, 1000);
    $('#accountsgaugesection').show('slide', {direction: 'right'}, 1000);
    var accountid=$(this).attr('data-id');
    $.ajax({

        type: 'POST',

        url: '../FinanceRequests/AccountitemCost',
        beforeSend : function(){

            $('.preloader').show();

        },

        dataType: "json",

        data: {accountid:accountid,project:$('#findashproject').val()},

        success: function(data){

            if(data.error=='No')

            {
                
                $('.preloader').hide();

                $('#dashpeitemsacnthd').html(data.datarows);
                $('#pedashaciteminfo').html(data.accountinfo);

                var actualqty=data.actqty;
                var estqty=data.estqty;
                var actualrate=data.actrate;
                var estrate=data.estrate;
                var estratestr=data.est_numstr;
                var actualratestr=data.act_numstr_act;
                var estresourceunit=data.estresourceunit;
                var newestimaterate=data.newestimaterate;
                var newactualrate=data.newactualrate;
                var actyestcons = data.actyestcons;
                var actyactualcons = data.actyactualcons;
                var avtest_qty_act = data.avtest_qty_act;
                var avtact_qty_act = data.avtact_qty_act;
                var avtest_rate = data.avtest_rate;
                var avtact_rate = data.avtact_rate;
                var newestimateratetot=data.newestimateratetot;
                var newactualratetot=data.newactualratetot;
                var avtest_numstr = data.avtest_numstr;
                var avtact_numstr_act = data.avtact_numstr_act;
                google.charts.load('current', {'packages':['gauge']});
                google.charts.setOnLoadCallback(eval("drawChart"));
                google.charts.setOnLoadCallback(eval("drawChart1"));
                google.charts.setOnLoadCallback(eval("drawChart2"));
                google.charts.setOnLoadCallback(eval("drawChart3"));
                function drawChart() {
                    var actual = actualqty * 1;
                    var data = google.visualization.arrayToDataTable([
                        ['Label', 'Value'],
                        ['', actual]
                    ]);
                    //var estcost = estqty / 4;
                    var estcost = (estqty * 1) / 3;
                    //console.log(estcost)
                    var majtricks = ['0'];
                    var numc=0;
                    for (var i=0;i<6;i++){
                        if (estcost < 1){
                            numc = numc + estcost;
                            majtricks.push(Math.floor(numc * 100) / 100)
                        }
                        else {
                            numc = numc + estcost;
                            majtricks.push(Math.round(numc))
                        }
                    }
                    //console.log(majtricks)
                    var options = {
                        //width: 400, height: 120,
                        min: 0, max: majtricks[6],
                        greenFrom: 0, greenTo: majtricks[3],
                        redFrom: majtricks[3], redTo: majtricks[6],
                        //yellowFrom:75, yellowTo: 90,
                        //majorTicks: ['0', '25', '50', '75', '100', '125', '150', '175', '200'],
                        majorTicks: majtricks,
                        minorTicks: 6
                    };
                    var formatoptions = new google.visualization.NumberFormat({
                        suffix: '',
                        fractionDigits: 2
                    });
                    formatoptions.format(data, 1);
                    $('#gaugechartacnthd').show();
                    $('#estcons').show().html("E.avg consumption: "+majtricks[3]+" "+estresourceunit);
                    $('#actcons').show().html("A.avg consumption: "+actual+" "+estresourceunit);
                    var chart = new google.visualization.Gauge(document.getElementById('gaugechartacnthd'));
                    chart.draw(data, options);

                }
                function drawChart1() {
                    var actual = actualrate * 1;
                    var data = google.visualization.arrayToDataTable([
                        ['Label', 'Value'],
                        ['', actual]
                    ]);
                    //var estcost = estrate / 4;
                    var estcost = (estrate * 1) / 3;
                    //console.log(estcost)
                    var majtricks = ['0'];
                    var numc=0;
                    for (var i=0;i<6;i++){
                        if (estcost < 1){
                            numc = numc + estcost;
                            majtricks.push(Math.floor(numc * 100) / 100)
                        }
                        else {
                            numc = numc + estcost;
                            majtricks.push(Math.round(numc))
                        }
                    }
                    //console.log(majtricks)
                    var options = {
                        //width: 400, height: 120,
                        min: 0, max: majtricks[6],
                        greenFrom: 0, greenTo: majtricks[3],
                        redFrom: majtricks[3], redTo: majtricks[6],
                        //yellowFrom:75, yellowTo: 90,
                        //majorTicks: ['0', '25', '50', '75', '100', '125', '150', '175', '200'],
                        majorTicks: majtricks,
                        minorTicks: 6
                    };
                    var formatoptions = new google.visualization.NumberFormat({
                        suffix: '',
                        fractionDigits: 1
                    });
                    formatoptions.format(data, 1);
                    $('#gaugechartacnthdrate').show();
                    $('#estrateitem').show().html("E.avg rate: "+newestimaterate+" "+estresourceunit);
                    $('#actrateitem').show().html("A.avg rate: "+newactualrate+" "+estresourceunit);
                    var chart = new google.visualization.Gauge(document.getElementById('gaugechartacnthdrate'));
                    chart.draw(data, options);

                }
                function drawChart2() {
                    var actual = actyactualcons * 1;
                    var data = google.visualization.arrayToDataTable([
                        ['Label', 'Value'],
                        ['', actual]
                    ]);
                    //var estcost = estqty / 4;
                    var estcost = (actyestcons * 1) / 3;
                    //console.log(estcost)
                    var majtricks = ['0'];
                    var numc=0;
                    for (var i=0;i<6;i++){
                        if (estcost < 1){
                            numc = numc + estcost;
                            majtricks.push(Math.floor(numc * 100) / 100)
                        }
                        else {
                            numc = numc + estcost;
                            majtricks.push(Math.round(numc))
                        }
                    }
                    //console.log(majtricks)
                    var options = {
                        //width: 400, height: 120,
                        min: 0, max: majtricks[6],
                        greenFrom: 0, greenTo: majtricks[3],
                        redFrom: majtricks[3], redTo: majtricks[6],
                        //yellowFrom:75, yellowTo: 90,
                        //majorTicks: ['0', '25', '50', '75', '100', '125', '150', '175', '200'],
                        majorTicks: majtricks,
                        minorTicks: 6
                    };
                    var formatoptions = new google.visualization.NumberFormat({
                        suffix: '',
                        fractionDigits: 2
                    });
                    formatoptions.format(data, 1);
                    $('#gaugechartactres').show();
                    $('#avtestcons').show().html("E.consumption: "+avtest_qty_act+" "+estresourceunit);
                    $('#avtactcons').show().html("A.consumption: "+avtact_qty_act+" "+estresourceunit);
                    var chart = new google.visualization.Gauge(document.getElementById('gaugechartactres'));
                    chart.draw(data, options);

                }
                function drawChart3() {
                    var actual = avtact_rate * 1;
                    var data = google.visualization.arrayToDataTable([
                        ['Label', 'Value'],
                        ['', actual]
                    ]);
                    //var estcost = estrate / 4;
                    var estcost = (avtest_rate * 1) / 3;
                    //console.log(estcost)
                    var majtricks = ['0'];
                    var numc=0;
                    for (var i=0;i<6;i++){
                        if (estcost < 1){
                            numc = numc + estcost;
                            majtricks.push(Math.floor(numc * 100) / 100)
                        }
                        else {
                            numc = numc + estcost;
                            majtricks.push(Math.round(numc))
                        }
                    }
                    //console.log(majtricks)
                    var options = {
                        //width: 400, height: 120,
                        min: 0, max: majtricks[6],
                        greenFrom: 0, greenTo: majtricks[3],
                        redFrom: majtricks[3], redTo: majtricks[6],
                        //yellowFrom:75, yellowTo: 90,
                        //majorTicks: ['0', '25', '50', '75', '100', '125', '150', '175', '200'],
                        majorTicks: majtricks,
                        minorTicks: 6
                    };
                    var formatoptions = new google.visualization.NumberFormat({
                        suffix: '',
                        fractionDigits: 2
                    });
                    formatoptions.format(data, 1);
                    $('#dialchartavtrate').show();
                    $('#avtestrateitem').show().html("E.rate: "+avtest_rate+" "+avtest_numstr);
                    $('#avtactrateitem').show().html("A.rate: "+avtact_rate+" "+avtact_numstr_act);
                    var chart = new google.visualization.Gauge(document.getElementById('dialchartavtrate'));
                    chart.draw(data, options);

                }

            }

        }

    });
});

function getacntheaddialchart(subgrpid)
{
    $.ajax({

        type: 'POST',

        url: '../FinanceRequests/Accountsdial',

        dataType: "json",

        data: {subgrpid:subgrpid,project:$('#findashproject').val()},

        success: function(data){

            if(data.error=='No')

            {
                $('.acnthdcolumnchart').hide();
                $('.estamtacnthd').hide();
                $('.actamtacnthd').hide();

                var greendata = data.greenrows;
                var datarr = greendata.split("|");
                var length=datarr.length;
                var greendatanew = data.greenrowsnew;
                var datarrnew = greendatanew.split("|");
                var eststrdata = data.estnumstrrows;
                var eststrdatarr = eststrdata.split("|");
                var actdata = data.actgrprows;
                var actdatanew = data.actgrprowsnew;
                var actdatarr = actdata.split("|");
                var actdatarrnew = actdatanew.split("|");
                var actstrdata = data.actnumstrrows;
                var actstrdatarr = actstrdata.split("|");
                var actsbname = data.subgrpnamerows;
                var actsbdatarr = actsbname.split("|");
                var count = length -1;
                //var length=datarr.length;
                google.charts.load('current', {'packages':['gauge']});
                for (var k=0;k<=count;k++){
                    //var number=k+1;
                    google.charts.setOnLoadCallback(eval("drawChart"+k));

                }
                
                function drawChart0() {
                    if(actstrdatarr[0] == 'lac') {
                        var actual = actdatarr[0]*1;
                    }
                    else {
                        var x = Math.round(actdatarrnew[0]);
                        var length = x.toString().length;
                        if(length == 4 || length == 5){
                            var actual = actdatarrnew[0]*1;
                        }
                        else {
                            var actual = actdatarr[0]*1;
                        }
                    }
                    if(eststrdatarr[0] == 'lac') {
                        var estimate0 = datarr[0]*1;
                    }
                    else {
                        var y = Math.round(datarrnew[0]);
                        var estlength = y.toString().length;
                        if(estlength == 4 || estlength == 5){
                            var estimate0 = datarrnew[0]*1;
                        }
                        else {
                            var estimate0 = datarr[0]*1;
                        }
                    }
                    
                    var data = google.visualization.arrayToDataTable([
                        ['Label', 'Value'],
                        ['', actual]
                    ]);
                    //var estcost = datarr[0] / 4;
                    var estcost = (datarr[0] * 1) / 3;
                    //console.log(estcost)
                    var majtricks = ['0'];
                    var numc=0;
                    for (var i=0;i<6;i++){
                        if (estcost < 1){
                            numc = numc + estcost;
                            majtricks.push(Math.floor(numc * 100) / 100)
                        }
                        else {
                            numc = numc + estcost;
                            majtricks.push(Math.round(numc))
                        }
                    }
                    //console.log(majtricks)
                    var options = {
                        //width: 400, height: 120,
                        min: 0, max: majtricks[6],
                        greenFrom: 0, greenTo: majtricks[3],
                        redFrom: majtricks[3], redTo: majtricks[6],
                        //yellowFrom:75, yellowTo: 90,
                        //majorTicks: ['0', '25', '50', '75', '100', '125', '150', '175', '200'],
                        majorTicks: majtricks,
                        minorTicks: 6
                    };
                    var formatoptions = new google.visualization.NumberFormat({
                        suffix: actstrdatarr[0],
                        fractionDigits: 2
                    });
                    formatoptions.format(data, 1);
                    $('#acnthdcolumnchart0').show();
                    $('#acnthdname0').show().html(actsbdatarr[0]);
                    $('#estamtacnthd0').show().html("E.amount: "+estimate0+" "+eststrdatarr[0]);
                    $('#actamtacnthd0').show().html("A.amount: "+actual+" "+actstrdatarr[0]);
                    var chart = new google.visualization.Gauge(document.getElementById('acnthdcolumnchart0'));
                    chart.draw(data, options);

                }
                function drawChart1() {
                    var x = Math.round(actdatarrnew[1]);
                    var length = x.toString().length;
                    if(length == 4 || length == 5){
                        var actual1 = actdatarrnew[1]*1;
                    }
                    else {
                        var actual1 = actdatarr[1]*1;
                    }
                    //var actual1 = actdatarr[1]*1;
                    if(eststrdatarr[1] == 'lac') {
                        var estimate1 = datarr[1]*1;
                    }
                    else {
                        var y = Math.round(datarrnew[1]);
                        var estlength = y.toString().length;
                        if(estlength == 4 || estlength == 5){
                            var estimate1 = datarrnew[1]*1;
                        }
                        else {
                            var estimate1 = datarr[1]*1;
                        }
                    }
                    var actualdial1 = actdatarr[1]*1;
                    var data = google.visualization.arrayToDataTable([
                        ['Label', 'Value'],
                        ['', actualdial1]
                    ]);
                    //var estcost = datarr[1] / 4;
                    var estcost = (datarr[1] * 1) / 3;
                    //console.log(estcost)
                    var majtricks = ['0'];
                    var numc=0;
                    for (var i=0;i<6;i++){
                        if (estcost < 1){
                            numc = numc + estcost;
                            majtricks.push(Math.floor(numc * 100) / 100)
                        }
                        else {
                            numc = numc + estcost;
                            majtricks.push(Math.round(numc))
                        }
                    }
                    //console.log(majtricks)
                    var options = {
                        //width: 400, height: 120,
                        min: 0, max: majtricks[6],
                        greenFrom: 0, greenTo: majtricks[3],
                        redFrom: majtricks[3], redTo: majtricks[6],
                        //yellowFrom:75, yellowTo: 90,
                        //majorTicks: ['0', '25', '50', '75', '100', '125', '150', '175', '200'],
                        majorTicks: majtricks,
                        minorTicks: 6
                    };
                    var formatoptions = new google.visualization.NumberFormat({
                        suffix: actstrdatarr[1],
                        fractionDigits: 2
                    });
                    formatoptions.format(data, 1);
                    $('#acnthdcolumnchart1').show();
                    $('#acnthdname1').show().html(actsbdatarr[1]);
                    $('#estamtacnthd1').show().html("E.amount: "+estimate1+" "+eststrdatarr[1]);
                    $('#actamtacnthd1').show().html("A.amount: "+actual1+" "+actstrdatarr[1]);
                    var chart = new google.visualization.Gauge(document.getElementById('acnthdcolumnchart1'));
                    chart.draw(data, options);
                }
                function drawChart2() {
                    if(actstrdatarr[2] == 'lac') {
                        var actual2 = actdatarr[2]*1;
                    }
                    else {
                        var x = Math.round(actdatarrnew[2]);
                        var length = x.toString().length;
                        if(length == 4 || length == 5){
                            var actual2 = actdatarrnew[2]*1;
                        }
                        else {
                            var actual2 = actdatarr[2]*1;
                        }
                    }
                    //var actual2 = actdatarr[2]*1;
                    if(eststrdatarr[2] == 'lac') {
                        var estimate2 = datarr[2]*1;
                    }
                    else {
                        var y = Math.round(datarrnew[2]);
                        var estlength = y.toString().length;
                        if(estlength == 4 || estlength == 5){
                            var estimate2 = datarrnew[2]*1;
                        }
                        else {
                            var estimate2 = datarr[2]*1;
                        }
                    }
                    var actualdial2 = actdatarr[2]*1;
                    var data = google.visualization.arrayToDataTable([
                        ['Label', 'Value'],
                        ['', actualdial2]
                    ]);
                    //var estcost = datarr[2] / 4;
                    var estcost = (datarr[2] * 1) / 3;
                    //console.log(estcost)
                    var majtricks = ['0'];
                    var numc=0;
                    for (var i=0;i<6;i++){
                        if (estcost < 1){
                            numc = numc + estcost;
                            majtricks.push(Math.floor(numc * 100) / 100)
                        }
                        else {
                            numc = numc + estcost;
                            majtricks.push(Math.round(numc))
                        }
                    }
                    //console.log(majtricks)
                    var options = {
                        //width: 400, height: 120,
                        min: 0, max: majtricks[6],
                        greenFrom: 0, greenTo: majtricks[3],
                        redFrom: majtricks[3], redTo: majtricks[6],
                        //yellowFrom:75, yellowTo: 90,
                        //majorTicks: ['0', '25', '50', '75', '100', '125', '150', '175', '200'],
                        majorTicks: majtricks,
                        minorTicks: 6
                    };
                    var formatoptions = new google.visualization.NumberFormat({
                        suffix: actstrdatarr[2],
                        fractionDigits: 2
                    });
                    formatoptions.format(data, 1);
                    $('#acnthdcolumnchart2').show();
                    $('#acnthdname2').show().html(actsbdatarr[2]);
                    $('#estamtacnthd2').show().html("E.amount: "+estimate2+" "+eststrdatarr[2]);
                    $('#actamtacnthd2').show().html("A.amount: "+actual2+" "+actstrdatarr[2]);
                    var chart = new google.visualization.Gauge(document.getElementById('acnthdcolumnchart2'));
                    chart.draw(data, options);
                }
                function drawChart3() {
                    var x = Math.round(actdatarrnew[3]);
                    var length = x.toString().length;
                    if(length == 4 || length == 5){
                        var actual3 = actdatarrnew[3]*1;
                    }
                    else {
                        var actual3 = actdatarr[3]*1;
                    }
                    var y = Math.round(datarrnew[3]);
                    var estlength = y.toString().length;
                    if(estlength == 4 || estlength == 5){
                        var estimate3 = datarrnew[3]*1;
                    }
                    else {
                        var estimate3 = datarr[3]*1;
                    }
                    //var actual3 = actdatarr[3]*1;
                    var actualdial3 = actdatarr[3]*1;
                    var data = google.visualization.arrayToDataTable([
                        ['Label', 'Value'],
                        ['', actualdial3]
                    ]);
                    //var estcost = datarr[3] / 4;
                    var estcost = (datarr[3] * 1) / 3;
                    //console.log(estcost)
                    var majtricks = ['0'];
                    var numc=0;
                    for (var i=0;i<6;i++){
                        if (estcost < 1){
                            numc = numc + estcost;
                            majtricks.push(Math.floor(numc * 100) / 100)
                        }
                        else {
                            numc = numc + estcost;
                            majtricks.push(Math.round(numc))
                        }
                    }
                    //console.log(majtricks)
                    var options = {
                        //width: 400, height: 120,
                        min: 0, max: majtricks[6],
                        greenFrom: 0, greenTo: majtricks[3],
                        redFrom: majtricks[3], redTo: majtricks[6],
                        //yellowFrom:75, yellowTo: 90,
                        //majorTicks: ['0', '25', '50', '75', '100', '125', '150', '175', '200'],
                        majorTicks: majtricks,
                        minorTicks: 6
                    };
                    var formatoptions = new google.visualization.NumberFormat({
                        suffix: actstrdatarr[3],
                        fractionDigits: 2
                    });
                    formatoptions.format(data, 1);
                    $('#acnthdcolumnchart3').show();
                    $('#acnthdname3').show().html(actsbdatarr[3]);
                    $('#estamtacnthd3').show().html("E.amount: "+estimate3+" "+eststrdatarr[3]);
                    $('#actamtacnthd3').show().html("A.amount: "+actual3+" "+actstrdatarr[3]);
                    var chart = new google.visualization.Gauge(document.getElementById('acnthdcolumnchart3'));
                    chart.draw(data, options);
                }
                function drawChart4() {
                    if(actstrdatarr[4] == 'lac') {
                        var actual4 = actdatarr[4]*1;
                    }
                    else {
                        var x = Math.round(actdatarrnew[4]);
                        var length = x.toString().length;
                        if(length == 4 || length == 5){
                            var actual4 = actdatarrnew[4]*1;
                        }
                        else {
                            var actual4 = actdatarr[4]*1;
                        }
                    }
                    
                    //var actual4 = actdatarr[4]*1;
                    var y = Math.round(datarrnew[4]);
                    var estlength = y.toString().length;
                    if(estlength == 4 || estlength == 5){
                        var estimate4 = datarrnew[4]*1;
                    }
                    else {
                        var estimate4 = datarr[4]*1;
                    }
                    var data = google.visualization.arrayToDataTable([
                        ['Label', 'Value'],
                        ['', actual4]
                    ]);
                    //var estcost = datarr[4] / 4;
                    var estcost = (datarr[4] * 1) / 3;
                    //console.log(estcost)
                    var majtricks = ['0'];
                    var numc=0;
                    for (var i=0;i<6;i++){
                        if (estcost < 1){
                            numc = numc + estcost;
                            majtricks.push(Math.floor(numc * 100) / 100)
                        }
                        else {
                            numc = numc + estcost;
                            majtricks.push(Math.round(numc))
                        }
                    }
                    //console.log(majtricks)
                    var options = {
                        //width: 400, height: 120,
                        min: 0, max: majtricks[6],
                        greenFrom: 0, greenTo: majtricks[3],
                        redFrom: majtricks[3], redTo: majtricks[6],
                        //yellowFrom:75, yellowTo: 90,
                        //majorTicks: ['0', '25', '50', '75', '100', '125', '150', '175', '200'],
                        majorTicks: majtricks,
                        minorTicks: 6
                    };
                    var formatoptions = new google.visualization.NumberFormat({
                        suffix: actstrdatarr[4],
                        fractionDigits: 2
                    });
                    formatoptions.format(data, 1);
                    $('#acnthdcolumnchart4').show();
                    $('#acnthdname4').show().html(actsbdatarr[4]);
                    $('#estamtacnthd4').show().html("E.amount: "+estimate4+" "+eststrdatarr[4]);
                    $('#actamtacnthd4').show().html("A.amount: "+actual4+" "+actstrdatarr[4]);
                    var chart = new google.visualization.Gauge(document.getElementById('acnthdcolumnchart4'));
                    chart.draw(data, options);
                }
                function drawChart5() {
                    var x = Math.round(actdatarrnew[5]);
                    var length = x.toString().length;
                    if(length == 4 || length == 5){
                        var actual5 = actdatarrnew[5]*1;
                    }
                    else {
                        var actual5 = actdatarr[5]*1;
                    }
                    //var actual5 = actdatarr[5]*1;
                    var y = Math.round(datarrnew[5]);
                    var estlength = y.toString().length;
                    if(estlength == 4 || estlength == 5){
                        var estimate5 = datarrnew[5]*1;
                    }
                    else {
                        var estimate5 = datarr[5]*1;
                    }
                    var data = google.visualization.arrayToDataTable([
                        ['Label', 'Value'],
                        ['', actual5]
                    ]);
                    //var estcost = datarr[5] / 4;
                    var estcost = (datarr[5] * 1) / 3;
                    //console.log(estcost)
                    var majtricks = ['0'];
                    var numc=0;
                    for (var i=0;i<6;i++){
                        if (estcost < 1){
                            numc = numc + estcost;
                            majtricks.push(Math.floor(numc * 100) / 100)
                        }
                        else {
                            numc = numc + estcost;
                            majtricks.push(Math.round(numc))
                        }
                    }
                    //console.log(majtricks)
                    var options = {
                        //width: 400, height: 120,
                        min: 0, max: majtricks[6],
                        greenFrom: 0, greenTo: majtricks[3],
                        redFrom: majtricks[3], redTo: majtricks[6],
                        //yellowFrom:75, yellowTo: 90,
                        //majorTicks: ['0', '25', '50', '75', '100', '125', '150', '175', '200'],
                        majorTicks: majtricks,
                        minorTicks: 6
                    };
                    var formatoptions = new google.visualization.NumberFormat({
                        suffix: actstrdatarr[5],
                        fractionDigits: 2
                    });
                    formatoptions.format(data, 1);
                    $('#acnthdcolumnchart5').show();
                    $('#acnthdname5').show().html(actsbdatarr[5]);
                    $('#estamtacnthd5').show().html("E.amount: "+estimate5+" "+eststrdatarr[5]);
                    $('#actamtacnthd5').show().html("A.amount: "+actual5+" "+actstrdatarr[5]);
                    var chart = new google.visualization.Gauge(document.getElementById('acnthdcolumnchart5'));
                    chart.draw(data, options);
                }
                function drawChart6() {
                    var actual6 = actdatarr[6]*1;
                    var data = google.visualization.arrayToDataTable([
                        ['Label', 'Value'],
                        ['', actual6]
                    ]);
                    //var estcost = datarr[6] / 4;
                    var estcost = (datarr[6] * 1) / 3;
                    //console.log(estcost)
                    var majtricks = ['0'];
                    var numc=0;
                    for (var i=0;i<6;i++){
                        if (estcost < 1){
                            numc = numc + estcost;
                            majtricks.push(Math.floor(numc * 100) / 100)
                        }
                        else {
                            numc = numc + estcost;
                            majtricks.push(Math.round(numc))
                        }
                    }
                    //console.log(majtricks)
                    var options = {
                        //width: 400, height: 120,
                        min: 0, max: majtricks[6],
                        greenFrom: 0, greenTo: majtricks[3],
                        redFrom: majtricks[3], redTo: majtricks[6],
                        //yellowFrom:75, yellowTo: 90,
                        //majorTicks: ['0', '25', '50', '75', '100', '125', '150', '175', '200'],
                        majorTicks: majtricks,
                        minorTicks: 6
                    };
                    var formatoptions = new google.visualization.NumberFormat({
                        suffix: actstrdatarr[6],
                        fractionDigits: 2
                    });
                    formatoptions.format(data, 1);
                    $('#acnthdcolumnchart6').show();
                    $('#acnthdname6').show().html(actsbdatarr[6]);
                    $('#estamtacnthd6').show().html("E.amount: "+datarr[6]+" "+eststrdatarr[6]);
                    $('#actamtacnthd6').show().html("A.amount: "+actual6+" "+actstrdatarr[6]);
                    var chart = new google.visualization.Gauge(document.getElementById('acnthdcolumnchart6'));
                    chart.draw(data, options);
                }
                function drawChart7() {
                    var actual7 = actdatarr[7]*1;
                    var data = google.visualization.arrayToDataTable([
                        ['Label', 'Value'],
                        ['', actual7]
                    ]);
                    //var estcost = datarr[7] / 4;
                    var estcost = (datarr[7] * 1) / 3;
                    //console.log(estcost)
                    var majtricks = ['0'];
                    var numc=0;
                    for (var i=0;i<6;i++){
                        if (estcost < 1){
                            numc = numc + estcost;
                            majtricks.push(Math.floor(numc * 100) / 100)
                        }
                        else {
                            numc = numc + estcost;
                            majtricks.push(Math.round(numc))
                        }
                    }
                    //console.log(majtricks)
                    var options = {
                        //width: 400, height: 120,
                        min: 0, max: majtricks[6],
                        greenFrom: 0, greenTo: majtricks[3],
                        redFrom: majtricks[3], redTo: majtricks[6],
                        //yellowFrom:75, yellowTo: 90,
                        //majorTicks: ['0', '25', '50', '75', '100', '125', '150', '175', '200'],
                        majorTicks: majtricks,
                        minorTicks: 6
                    };
                    var formatoptions = new google.visualization.NumberFormat({
                        suffix: actstrdatarr[7],
                        fractionDigits: 2
                    });
                    formatoptions.format(data, 1);
                    $('#acnthdcolumnchart7').show();
                    $('#acnthdname7').show().html(actsbdatarr[7]);
                    $('#estamtacnthd7').show().html("E.amount: "+datarr[7]+" "+eststrdatarr[7]);
                    $('#actamtacnthd7').show().html("A.amount: "+actual7+" "+actstrdatarr[7]);
                    var chart = new google.visualization.Gauge(document.getElementById('acnthdcolumnchart7'));
                    chart.draw(data, options);

                }
                function drawChart8() {
                    if(actstrdatarr[4] == 'lac') {
                        var actual8 = actdatarr[8]*1;
                    }
                    else {
                        var x = Math.round(actdatarrnew[8]);
                        var length = x.toString().length;
                        if(length == 4 || length == 5){
                            var actual8 = actdatarrnew[8]*1;
                        }
                        else {
                            var actual8 = actdatarr[8]*1;
                        }
                    }
                    
                    //var actual8 = actdatarr[8]*1;
                    if(eststrdatarr[8] == 'lac') {
                        var estimate8 = datarr[8]*1;
                    }
                    else {
                        var y = Math.round(datarrnew[8]);
                        var estlength = y.toString().length;
                        if(estlength == 4 || estlength == 5){
                            var estimate8 = datarrnew[8]*1;
                        }
                        else {
                            var estimate8 = datarr[8]*1;
                        }
                    }
                    var actualdial8 = actdatarr[8]*1;
                    var data = google.visualization.arrayToDataTable([
                        ['Label', 'Value'],
                        ['', actualdial8]
                    ]);
                    //var estcost = datarr[8] / 4;
                    var estcost = (datarr[8] * 1) / 3;
                    //console.log(estcost)
                    var majtricks = ['0'];
                    var numc=0;
                    for (var i=0;i<6;i++){
                        if (estcost < 1){
                            numc = numc + estcost;
                            majtricks.push(Math.floor(numc * 100) / 100)
                        }
                        else {
                            numc = numc + estcost;
                            majtricks.push(Math.round(numc))
                        }
                    }
                    //console.log(majtricks)
                    var options = {
                        //width: 400, height: 120,
                        min: 0, max: majtricks[6],
                        greenFrom: 0, greenTo: majtricks[3],
                        redFrom: majtricks[3], redTo: majtricks[6],
                        //yellowFrom:75, yellowTo: 90,
                        //majorTicks: ['0', '25', '50', '75', '100', '125', '150', '175', '200'],
                        majorTicks: majtricks,
                        minorTicks: 6
                    };
                    var formatoptions = new google.visualization.NumberFormat({
                        suffix: actstrdatarr[8],
                        fractionDigits: 2
                    });
                    formatoptions.format(data, 1);
                    $('#acnthdcolumnchart8').show();
                    $('#acnthdname8').show().html(actsbdatarr[8]);
                    $('#estamtacnthd8').show().html("E.amount: "+estimate8+" "+eststrdatarr[8]);
                    $('#actamtacnthd8').show().html("A.amount: "+actual8+" "+actstrdatarr[8]);
                    var chart = new google.visualization.Gauge(document.getElementById('acnthdcolumnchart8'));
                    chart.draw(data, options);

                }
                function drawChart9() {
                    var actual9 = actdatarr[9]*1;
                    var data = google.visualization.arrayToDataTable([
                        ['Label', 'Value'],
                        ['', actual9]
                    ]);
                    //var estcost = datarr[9] / 4;
                    var estcost = (datarr[9] * 1) / 3;
                    //console.log(estcost)
                    var majtricks = ['0'];
                    var numc=0;
                    for (var i=0;i<6;i++){
                        if (estcost < 1){
                            numc = numc + estcost;
                            majtricks.push(Math.floor(numc * 100) / 100)
                        }
                        else {
                            numc = numc + estcost;
                            majtricks.push(Math.round(numc))
                        }
                    }
                    //console.log(majtricks)
                    var options = {
                        //width: 400, height: 120,
                        min: 0, max: majtricks[6],
                        greenFrom: 0, greenTo: majtricks[3],
                        redFrom: majtricks[3], redTo: majtricks[6],
                        //yellowFrom:75, yellowTo: 90,
                        //majorTicks: ['0', '25', '50', '75', '100', '125', '150', '175', '200'],
                        majorTicks: majtricks,
                        minorTicks: 6
                    };
                    var formatoptions = new google.visualization.NumberFormat({
                        suffix: actstrdatarr[9],
                        fractionDigits: 2
                    });
                    formatoptions.format(data, 1);
                    $('#acnthdcolumnchart9').show();
                    $('#acnthdname9').show().html(actsbdatarr[9]);
                    $('#estamtacnthd9').show().html("E.amount: "+datarr[9]+" "+eststrdatarr[9]);
                    $('#actamtacnthd9').show().html("A.amount: "+actual9+" "+actstrdatarr[9]);
                    var chart = new google.visualization.Gauge(document.getElementById('acnthdcolumnchart9'));
                    chart.draw(data, options);

                }
                function drawChart10() {
                    var actual10 = actdatarr[10]*1;
                    var data = google.visualization.arrayToDataTable([
                        ['Label', 'Value'],
                        ['', actual10]
                    ]);
                    //var estcost = datarr[10] / 4;
                    var estcost = (datarr[10] * 1) / 3;
                    //console.log(estcost)
                    var majtricks = ['0'];
                    var numc=0;
                    for (var i=0;i<6;i++){
                        if (estcost < 1){
                            numc = numc + estcost;
                            majtricks.push(Math.floor(numc * 100) / 100)
                        }
                        else {
                            numc = numc + estcost;
                            majtricks.push(Math.round(numc))
                        }
                    }
                    //console.log(majtricks)
                    var options = {
                        //width: 400, height: 120,
                        min: 0, max: majtricks[6],
                        greenFrom: 0, greenTo: majtricks[3],
                        redFrom: majtricks[3], redTo: majtricks[6],
                        //yellowFrom:75, yellowTo: 90,
                        //majorTicks: ['0', '25', '50', '75', '100', '125', '150', '175', '200'],
                        majorTicks: majtricks,
                        minorTicks: 6
                    };
                    var formatoptions = new google.visualization.NumberFormat({
                        suffix: actstrdatarr[10],
                        fractionDigits: 2
                    });
                    formatoptions.format(data, 1);
                    $('#acnthdcolumnchart10').show();
                    $('#acnthdname10').show().html(actsbdatarr[10]);
                    $('#estamtacnthd10').show().html("E.amount: "+datarr[10]+" "+eststrdatarr[10]);
                    $('#actamtacnthd10').show().html("A.amount: "+actual10+" "+actstrdatarr[10]);
                    var chart = new google.visualization.Gauge(document.getElementById('acnthdcolumnchart10'));
                    chart.draw(data, options);

                }
                function drawChart11() {
                    var actual11 = actdatarr[11]*1;
                    var data = google.visualization.arrayToDataTable([
                        ['Label', 'Value'],
                        ['', actual11]
                    ]);
                    //var estcost = datarr[11] / 4;
                    var estcost = (datarr[11] * 1) / 3;
                    //console.log(estcost)
                    var majtricks = ['0'];
                    var numc=0;
                    for (var i=0;i<6;i++){
                        if (estcost < 1){
                            numc = numc + estcost;
                            majtricks.push(Math.floor(numc * 100) / 100)
                        }
                        else {
                            numc = numc + estcost;
                            majtricks.push(Math.round(numc))
                        }
                    }
                    //console.log(majtricks)
                    var options = {
                        //width: 400, height: 120,
                        min: 0, max: majtricks[6],
                        greenFrom: 0, greenTo: majtricks[3],
                        redFrom: majtricks[3], redTo: majtricks[6],
                        //yellowFrom:75, yellowTo: 90,
                        //majorTicks: ['0', '25', '50', '75', '100', '125', '150', '175', '200'],
                        majorTicks: majtricks,
                        minorTicks: 6
                    };
                    var formatoptions = new google.visualization.NumberFormat({
                        suffix: actstrdatarr[11],
                        fractionDigits: 2
                    });
                    formatoptions.format(data, 1);
                    $('#acnthdcolumnchart11').show();
                    $('#acnthdname11').show().html(actsbdatarr[11]);
                    $('#estamtacnthd11').show().html("E.amount: "+datarr[11]+" "+eststrdatarr[11]);
                    $('#actamtacnthd11').show().html("A.amount: "+actual11+" "+actstrdatarr[11]);
                    var chart = new google.visualization.Gauge(document.getElementById('acnthdcolumnchart11'));
                    chart.draw(data, options);

                }

            }

        }

    });
}

function getaccountsbarchart(subgrpid)
{
    $.ajax({

        type: 'POST',

        url: '../FinanceRequests/Monthwiseaccounts',

        dataType: "json",

        data: {subgrpid:subgrpid,project:$('#findashproject').val()},

        success: function(data){

            if(data.error=='No')

            {
                $('.acnthdcolumnchart').hide();
                //google.charts.load('current', {'packages':['bar']});
                google.charts.load("current", {packages:['corechart']});
                var result=data.result;
                var datarr = result.split("|");
                var length=datarr.length;

                //console.log(datarr);
                var subgrps=data.subgrprows;
                var name = subgrps.split("|");

                for (var i=0;i<length;i++)
                {
                    var number=i+1;
                    google.charts.setOnLoadCallback(eval("drawChart"+number));
                }

                function drawChart1() {
                    eval(datarr[0]);
                    var options = {title: name[0]+' (in months)',legend:'none',isStacked: true,animation: {startup:true,duration: 1000,easing: 'out'},annotations:{alwaysOutside:true,textStyle:{bold: true,color: '#ffffff'}}};
                    $('#acnthdcolumnchart0').show();
                    var chart = new google.visualization.ColumnChart(document.getElementById("acnthdcolumnchart0"));
                    chart.draw(subgrparr0, options);
                }
                function drawChart2() {
                    eval(datarr[1]);
                    var options = {title: name[1]+' (in months)',legend:'none',isStacked: true,animation: {startup:true,duration: 1000,easing: 'out'},annotations:{alwaysOutside:true,textStyle:{bold: true,color: '#ffffff'}}};
                    $('#acnthdcolumnchart1').show();
                    var chart = new google.visualization.ColumnChart(document.getElementById("acnthdcolumnchart1"));
                    chart.draw(subgrparr1, options);
                }
                function drawChart3() {
                    eval(datarr[2]);
                    var options = {title: name[2]+' (in months)',legend:'none',isStacked: true,animation: {startup:true,duration: 1000,easing: 'out'},annotations:{alwaysOutside:true,textStyle:{bold: true,color: '#ffffff'}}};
                    $('#acnthdcolumnchart2').show();
                    var chart = new google.visualization.ColumnChart(document.getElementById("acnthdcolumnchart2"));
                    chart.draw(subgrparr2, options);
                }
                function drawChart4() {
                    eval(datarr[3]);
                    var options = {title: name[3]+' (in months)',legend:'none',isStacked: true,animation: {startup:true,duration: 1000,easing: 'out'},annotations:{alwaysOutside:true,textStyle:{bold: true,color: '#ffffff'}}};
                    $('#acnthdcolumnchart3').show();
                    var chart = new google.visualization.ColumnChart(document.getElementById("acnthdcolumnchart3"));
                    chart.draw(subgrparr3, options);
                }
                function drawChart5() {
                    eval(datarr[4]);
                    var options = {title: name[4]+' (in months)',legend:'none',isStacked: true,animation: {startup:true,duration: 1000,easing: 'out'},annotations:{alwaysOutside:true,textStyle:{bold: true,color: '#ffffff'}}};
                    $('#acnthdcolumnchart4').show();
                    var chart = new google.visualization.ColumnChart(document.getElementById("acnthdcolumnchart4"));
                    chart.draw(subgrparr4, options);
                }
                function drawChart6() {
                    eval(datarr[5]);
                    var options = {title: name[5]+' (in months)',legend:'none',isStacked: true,animation: {startup:true,duration: 1000,easing: 'out'},annotations:{alwaysOutside:true,textStyle:{bold: true,color: '#ffffff'}}};
                    $('#acnthdcolumnchart5').show();
                    var chart = new google.visualization.ColumnChart(document.getElementById("acnthdcolumnchart5"));
                    chart.draw(subgrparr5, options);
                }
                function drawChart7() {
                    eval(datarr[6]);
                    var options = {title: name[6]+ ' (in months)',legend:'none',isStacked: true,animation: {startup:true,duration: 1000,easing: 'out'},annotations:{alwaysOutside:true,textStyle:{bold: true,color: '#ffffff'}}};
                    $('#acnthdcolumnchart6').show();
                    var chart = new google.visualization.ColumnChart(document.getElementById("acnthdcolumnchart6"));
                    chart.draw(subgrparr6, options);
                }
                function drawChart8() {
                    eval(datarr[7]);
                    var options = {title: name[7]+' (in months)',legend:'none',isStacked: true,animation: {startup:true,duration: 1000,easing: 'out'},annotations:{alwaysOutside:true,textStyle:{bold: true,color: '#ffffff'}}};
                    $('#acnthdcolumnchart7').show();
                    var chart = new google.visualization.ColumnChart(document.getElementById("acnthdcolumnchart7"));
                    chart.draw(subgrparr7, options);
                }
                function drawChart9() {
                    eval(datarr[8]);
                    var options = {title: name[8]+' (in months)',legend:'none',isStacked: true,animation: {startup:true,duration: 1000,easing: 'out'},annotations:{alwaysOutside:true,textStyle:{bold: true,color: '#ffffff'}}};
                    $('#acnthdcolumnchart8').show();
                    var chart = new google.visualization.ColumnChart(document.getElementById("acnthdcolumnchart8"));
                    chart.draw(subgrparr8, options);
                }
                function drawChart10() {
                    eval(datarr[9]);
                    //var options = {title: name[9]+' Expense (in months)',legend:'bottom',colors: ['#61d836']};
                    var options = {title: name[9]+' (in months)',legend:'none',isStacked: true,animation: {startup:true,duration: 1000,easing: 'out'},annotations:{alwaysOutside:true,textStyle:{bold: true,color: '#ffffff'}}};
                    $('#acnthdcolumnchart9').show();
                    var chart = new google.visualization.ColumnChart(document.getElementById("acnthdcolumnchart9"));
                    chart.draw(subgrparr9, options);
                }
                function drawChart11() {
                    eval(datarr[10]);
                    //var options = {title: name[9]+' Expense (in months)',legend:'bottom',colors: ['#61d836']};
                    var options = {title: name[10]+' (in months)',legend:'none',isStacked: true,animation: {startup:true,duration: 1000,easing: 'out'},annotations:{alwaysOutside:true,textStyle:{bold: true,color: '#ffffff'}}};
                    $('#acnthdcolumnchart10').show();
                    var chart = new google.visualization.ColumnChart(document.getElementById("acnthdcolumnchart10"));
                    chart.draw(subgrparr10, options);
                }
                function drawChart12() {
                    eval(datarr[11]);
                    //var options = {title: name[9]+' Expense (in months)',legend:'bottom',colors: ['#61d836']};
                    var options = {title: name[11]+' (in months)',legend:'none',isStacked: true,animation: {startup:true,duration: 1000,easing: 'out'},annotations:{alwaysOutside:true,textStyle:{bold: true,color: '#ffffff'}}};
                    $('#acnthdcolumnchart11').show();
                    var chart = new google.visualization.ColumnChart(document.getElementById("acnthdcolumnchart11"));
                    chart.draw(subgrparr11, options);
                }
                function drawChart13() {
                    eval(datarr[12]);
                    //var options = {title: name[9]+' Expense (in months)',legend:'bottom',colors: ['#61d836']};
                    var options = {title: name[12]+' (in months)',legend:'none',isStacked: true,animation: {startup:true,duration: 1000,easing: 'out'},annotations:{alwaysOutside:true,textStyle:{bold: true,color: '#ffffff'}}};
                    $('#acnthdcolumnchart12').show();
                    var chart = new google.visualization.ColumnChart(document.getElementById("acnthdcolumnchart12"));
                    chart.draw(subgrparr12, options);
                }
                function drawChart14() {
                    eval(datarr[13]);
                    //var options = {title: name[9]+' Expense (in months)',legend:'bottom',colors: ['#61d836']};
                    var options = {title: name[13]+' (in months)',legend:'none',isStacked: true,animation: {startup:true,duration: 1000,easing: 'out'},annotations:{alwaysOutside:true,textStyle:{bold: true,color: '#ffffff'}}};
                    $('#acnthdcolumnchart13').show();
                    var chart = new google.visualization.ColumnChart(document.getElementById("acnthdcolumnchart13"));
                    chart.draw(subgrparr13, options);
                }
                function drawChart15() {
                    eval(datarr[14]);
                    //var options = {title: name[9]+' Expense (in months)',legend:'bottom',colors: ['#61d836']};
                    var options = {title: name[14]+' (in months)',legend:'none',isStacked: true,animation: {startup:true,duration: 1000,easing: 'out'},annotations:{alwaysOutside:true,textStyle:{bold: true,color: '#ffffff'}}};
                    $('#acnthdcolumnchart14').show();
                    var chart = new google.visualization.ColumnChart(document.getElementById("acnthdcolumnchart14"));
                    chart.draw(subgrparr14, options);
                }
            }
        }
    });

}