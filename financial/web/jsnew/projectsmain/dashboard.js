/*$(function(){

    getprjctactivitycostchart();
    getprjctresourcecostchart();

});*/

$(document).on( "click", ".chart-win-close", function(){
  $('.icon-dashboard').removeClass('active');
  $('#project-title-head').html('Projects');
});

$(document).on( "click", ".prjctresource-cost-main", function(){

    //$('.mainlist').hide('slide', {direction: 'left'}, 1000);
    $('.mainlist').hide();
    $('.drilldownchart2').show('slide', {direction: 'right'}, 1000);

    getprjctresourcetypecostchart();

});

$(function(){

    $('#listdasboard').click(function(){ 

        getprjctactivitycostchart();
        getprjctresourcecostchart();
        getprjctdriverscostchart();
        getprjctcompletedatechart();
        getcapctyutiliztnchart();
        getprojectroichart();
        $('.drilldownchart1').hide();
        $('.drilldown2chart1').hide();
        $('.drilldownchart2').hide();
        $('.drilldown2chart2').hide();
        $('.drilldownchart3').hide();
        $('.drilldownchart5').hide();
        $('.mainlist').show();
        //getprjctresourcecostchart();

    });

});

$(document).on( "click", ".prjctactivity-cost", function(){

    //$('.mainlist').hide('slide', {direction: 'left'}, 1000);
    $('.mainlist').hide();
    $('.drilldownchart1').show('slide', {direction: 'right'}, 1000);

    getiowcostchart();

});

$(document).on( "click", ".mainlistback", function(){

    //$('.mainlist').hide('slide', {direction: 'left'}, 1000);
    $('.drilldownchart1').hide();
    $('.drilldownchart2').hide();
    $('.drilldownchart3').hide();
    $('.drilldownchart5').hide();
    $('.mainlist').show('slide', {direction: 'left'}, 1000);

});

$(document).on( "click", ".firstlistback", function(){

    //$('.mainlist').hide('slide', {direction: 'left'}, 1000);
    $('.drilldown2chart1').hide();
    $('.drilldownchart1').show('slide', {direction: 'right'}, 1000);

});

$(document).on( "click", ".firstlistbackchart2", function(){

    //$('.mainlist').hide('slide', {direction: 'left'}, 1000);
    $('.drilldown2chart2').hide();
    $('.drilldownchart2').show('slide', {direction: 'right'}, 1000);

});

$(document).on( "click", ".prjctdrivers-cost", function(){

    //$('.mainlist').hide('slide', {direction: 'left'}, 1000);
    $('.mainlist').hide();
    $('.drilldownchart3').show('slide', {direction: 'right'}, 1000);

    

});

function getprjctactivitycostchart()
{

    $.ajax({ 

        type: 'POST',

        url: '../dashboard/activitycost',

        beforeSend : function(){

            $('.preloader').show();

        },

        dataType: "json",

        data: {},

        success: function(data2){

            if(data2.error=='No')

            {
                $("#prj_name").html('Project Dashboard - '+data2.project); 
                
                google.charts.load('current', {'packages':['corechart']});
                google.charts.setOnLoadCallback(drawChart);

                function drawChart() {

                    var data = new google.visualization.DataTable();
                    data.addColumn({ type: 'string', id: 'Role' });
                    data.addColumn('number', '');

                    eval(data2.result);

                    /*var data = google.visualization.arrayToDataTable([
                      ['Task', 'Hours per Day'],
                      ['Work',     1000],
                      ['Eat',      20]
                    ]);*/

                    var options = {
                        backgroundColor: "#1e202c",
                        legend: { position: 'bottom' },
                      //title: 'Activity Cost',
                      //width: 450, height: 450,
                    };

                    var chart = new google.visualization.PieChart(document.getElementById('prjctactivity-cost'));

                    chart.draw(data, options);
                }

            }
            else{
                $('#errormessage').html(data2.result);
            }

            $('.preloader').hide();

        }

    });

}

function getprjctresourcecostchart()
{

    $.ajax({ 

        type: 'POST',

        url: '../dashboard/projectresourcecost',

        beforeSend : function(){

            $('.preloader').show();

        },

        dataType: "json",

        data: {},

        success: function(data1){

            if(data1.error=='No')

            {
                //$("#prj_name").html('Project Dashboard - '+data2.project); 
                
                google.charts.load('current', {'packages':['corechart']});
                google.charts.setOnLoadCallback(drawChart);

                function drawChart() {

                    var data = new google.visualization.DataTable();
                    data.addColumn({ type: 'string', id: 'Role' });
                    data.addColumn('number', '');

                    eval(data1.rescost);

                    /*var data = google.visualization.arrayToDataTable([
                      ['Task', 'Hours per Day'],
                      ['Work',     1000],
                      ['Eat',      20]
                    ]);*/

                    var options = {
                        backgroundColor: "#1e202c",
                        legend: { position: 'bottom' },
                        colors: ['green'],
                        /*chartArea: {
                          top: 0,
                        },*/
                      //title: 'Activity Cost',
                      //width: 450, height: 450,
                    };

                    var chart = new google.visualization.PieChart(document.getElementById('prjctresource-cost-main'));

                    chart.draw(data, options);

                }

            }

        }

    });

}

function getprjctresourcetypecostchart()
{

    $.ajax({ 

        type: 'POST',

        url: '../dashboard/projectresourcecost',

        beforeSend : function(){

            $('.preloader').show();

        },

        dataType: "json",

        data: {},

        success: function(data2){

            if(data2.error=='No')

            {
                //$("#prj_name").html('Project Dashboard - '+data2.project); 
                
                google.charts.load('current', {'packages':['corechart']});
                google.charts.setOnLoadCallback(drawChart);

                function drawChart() {

                    var data = new google.visualization.DataTable();
                    data.addColumn({ type: 'string', id: 'Role' });
                    data.addColumn('number', '');

                    eval(data2.result);

                    var count  = data.getNumberOfRows();
                    var values = Array(count).fill().map(function(v, i) {
                      return data.getValue(i, 1);
                    });
                    var total =  google.visualization.data.sum(values);
                    values.forEach(function(v, i) {                                         
                      var key = data.getValue(i, 0);
                      var val = data.getValue(i, 1);
                      data.setFormattedValue(i, 0, key + ' (' + (val/total * 100).toFixed(1) + '%)');                  
                    });

                    var options = {
                        backgroundColor: "#1e202c",
                        legend: { position: 'right' },
                        tooltip: { text: 'none' },
                        pieSliceText: 'none',
                        sliceVisibilityThreshold: 0,
                        //pieStartAngle: 90,
                        chartArea: {
                            bottom: 24,
                            height: '100%',
                            left: 10,
                            right: 0,
                            top: 24,
                            width: '100%'
                        },
                    };

                    var chart = new google.visualization.PieChart(document.getElementById('prjctresource-cost'));

                    function selectHandler() {
                      var selectedItem = chart.getSelection()[0];
                      if (selectedItem) {
                        var topping = data.getValue(selectedItem.row, 0);
                        $.ajax({
                          type: 'POST',
                          url: '../dashboard/projectresourcetypecost',
                          dataType: "json",
                          data: {resourcetype:topping},
                          success: function(data3){

                            google.charts.load('current', {'packages':['corechart']});
                            google.charts.setOnLoadCallback(drawChartdrill);

                            function drawChartdrill() {

                                var drilldata = new google.visualization.DataTable();
                                drilldata.addColumn({ type: 'string', id: 'Role' });
                                drilldata.addColumn('number', '');

                                eval(data3.result);

                                var count  = drilldata.getNumberOfRows();
                                var values = Array(count).fill().map(function(v, i) {
                                  return drilldata.getValue(i, 1);
                                });
                                var total =  google.visualization.data.sum(values);
                                values.forEach(function(v, i) {                                         
                                  var key = drilldata.getValue(i, 0);
                                  var val = drilldata.getValue(i, 1);
                                  drilldata.setFormattedValue(i, 0, key + ' (' + (val/total * 100).toFixed(1) + '%)');                  
                                });

                                //drilldata.sort([{column: 1}]);

                                var optionsdrill = {
                                    backgroundColor: "#1e202c",
                                    legend: { position: 'right' },
                                    tooltip: { text: 'none' },
                                    pieSliceText: 'none',
                                    sliceVisibilityThreshold: 0,
                                    //pieStartAngle: 90,
                                    chartArea: {
                                      bottom: 24,
                                      height: '100%',
                                      left: 10,
                                      right: 0,
                                      top: 24,
                                      width: '100%'
                                    },
                                  //title: 'Activity Cost',
                                  //width: 450, height: 450,
                                };

                                $('.drilldownchart2').hide();
                                $('.drilldown2chart2').show('slide', {direction: 'right'}, 1000);

                                var chartdrill = new google.visualization.PieChart(document.getElementById('resourcetype-cost')); 

                                /*function selectHandler2() {
                                  var selectedItem = chartdrill.getSelection()[0];
                                  if (selectedItem) 
                                    {
                                        var topping = drilldata.getValue(selectedItem.row, 0);
                                        $.ajax({
                                          type: 'POST',
                                          url: '../dashboard/projectresourcedial',
                                          dataType: "json",
                                          data: {resourcename:topping},
                                          success: function(data4){

                                            google.charts.load('current', {'packages':['gauge']});
                                            google.charts.setOnLoadCallback(drawChartdrill2);

                                            function drawChartdrill2() {

                                                var avgrate = data4.resavg_rate;
                                                var drilldata2A = google.visualization.arrayToDataTable([
                                                              ['Label', 'Value'],
                                                              ['', avgrate]
                                                          ]);

                                                var estcostamt = avgrate + avgrate;
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

                                                var optionsdrill2A = {
                                                    backgroundColor: "#1e202c",
                                                    min: 0, max: majtricks[6],
                                                    greenFrom: 0, greenTo: majtricks[3],
                                                    redFrom: majtricks[3], redTo: majtricks[6],
                                                    majorTicks: majtricks,
                                                  //title: 'Activity Cost',
                                                  //width: 450, height: 450,
                                                };

                                                var avgqty = data4.res_qty;
                                                var drilldata2B = google.visualization.arrayToDataTable([
                                                              ['Label', 'Value'],
                                                              ['', avgqty]
                                                          ]);

                                                var estcostamtB = avgqty + avgqty;
                                                var estcostB = (estcostamtB * 1) / 3;
                                                //console.log(estcost)
                                                var majtricksB = ['0'];
                                                var numcB=0;
                                                for (var i=0;i<6;i++){
                                                    if (estcostB < 1){
                                                        numcB = numcB + estcostB;
                                                        majtricksB.push(Math.floor(numcB * 100) / 100)
                                                    }
                                                    else {
                                                        numcB = numcB + estcostB;
                                                        majtricksB.push(Math.round(numcB))
                                                    }
                                                }

                                                var optionsdrill2B = {
                                                    backgroundColor: "#1e202c",
                                                    min: 0, max: majtricksB[6],
                                                    greenFrom: 0, greenTo: majtricksB[3],
                                                    redFrom: majtricksB[3], redTo: majtricksB[6],
                                                    majorTicks: majtricksB,
                                                  //title: 'Activity Cost',
                                                  //width: 450, height: 450,
                                                };

                                                $('.drilldownchart2').hide();
                                                $('.drilldown2chart2').show('slide', {direction: 'right'}, 1000);
                                                $('.avgresrateunit').show().html("Avg.Rate: "+data4.resourceavgrate+" "+data4.resunit);
                                                $('.avgresqtyunit').show().html("Qty: "+data4.resourceqty+" "+data4.resunit);
                                                var chartdrill2A = new google.visualization.Gauge(document.getElementById('avgresrate-cost')); 
                                                chartdrill2A.draw(drilldata2A, optionsdrill2A);

                                                var chartdrill2B = new google.visualization.Gauge(document.getElementById('avgresqty-cost')); 
                                                chartdrill2B.draw(drilldata2B, optionsdrill2B);

                                            }

                                          }

                                        });

                                    }

                                }

                                google.visualization.events.addListener(chartdrill, 'select', selectHandler2);*/

                                chartdrill.draw(drilldata, optionsdrill);

                                $('#restypename').html(data3.restypename);

                                $('#resource-names-list').html(data3.resourcelist);

                            }

                          }
                        });
                      }
                    }

                    google.visualization.events.addListener(chart, 'select', selectHandler);

                    chart.draw(data, options);

                    //$('#resourcetypename').html('Resource Type Cost Chart');

                    $('#resourcetype-names-list').html(data2.resourcelist);
                }

            }
            else{
                $('#errormessage').html(data2.result);
            }

            $('.preloader').hide();

        }

    });

}

function getiowcostchart()
{

    $.ajax({ 

        type: 'POST',

        url: '../dashboard/estimateiowcost',

        beforeSend : function(){

            $('.preloader').show();

        },

        dataType: "json",

        data: {},

        success: function(data2){

            if(data2.error=='No')

            {
                
                google.charts.load('current', {'packages':['corechart']});
                google.charts.setOnLoadCallback(drawChart);

                function drawChart() {

                    var data = new google.visualization.DataTable();
                    data.addColumn({ type: 'string', id: 'Role' });
                    data.addColumn('number', '');

                    eval(data2.result);

                    var count  = data.getNumberOfRows();
                    var values = Array(count).fill().map(function(v, i) {
                      return data.getValue(i, 1);
                    });
                    var total =  google.visualization.data.sum(values);
                    values.forEach(function(v, i) {                                         
                      var key = data.getValue(i, 0);
                      var val = data.getValue(i, 1);
                      data.setFormattedValue(i, 0, key + ' (' + (val/total * 100).toFixed(1) + '%)');                  
                    });

                    //data.sort([{column: 1}]);

                    /*var data = google.visualization.arrayToDataTable([
                      ['Task', 'Hours per Day'],
                      ['Work',     1000],
                      ['Eat',      20]
                    ]);*/

                    var options = {
                        backgroundColor: "#1e202c",
                        //legend: { position: 'labeled', alignment:'center' },
                        legend: { position: 'right', },
                        tooltip: { text: 'none' },
                        pieSliceText: 'none',
                        sliceVisibilityThreshold: 0,
                        //pieStartAngle: 90,
                        chartArea: {
                          bottom: 24,
                          height: '100%',
                          left: 10,
                          right: 0,
                          top: 24,
                          width: '100%'
                        },
                      //title: 'Activity Cost',
                      //width: 450, height: 450,
                    };

                    var chart = new google.visualization.PieChart(document.getElementById('iow-cost'));                   

                    function selectHandler() {
                      var selectedItem = chart.getSelection()[0];
                      if (selectedItem) {
                        var topping = data.getValue(selectedItem.row, 0);
                        $.ajax({
                          type: 'POST',
                          url: '../dashboard/estimateactivitycost',
                          dataType: "json",
                          data: {iowname:topping},
                          success: function(data3){

                            google.charts.load('current', {'packages':['corechart']});
                            google.charts.setOnLoadCallback(drawChartdrill);

                            function drawChartdrill() {

                                var drilldata = new google.visualization.DataTable();
                                drilldata.addColumn({ type: 'string', id: 'Role' });
                                drilldata.addColumn('number', '');

                                eval(data3.result);

                                //drilldata.sort([{column: 1}]);

                                /*var data = google.visualization.arrayToDataTable([
                                  ['Task', 'Hours per Day'],
                                  ['Work',     1000],
                                  ['Eat',      20]
                                ]);*/

                                var count  = drilldata.getNumberOfRows();
                                var values = Array(count).fill().map(function(v, i) {
                                  return drilldata.getValue(i, 1);
                                });
                                var total =  google.visualization.data.sum(values);
                                values.forEach(function(v, i) {                                         
                                  var key = drilldata.getValue(i, 0);
                                  var val = drilldata.getValue(i, 1);
                                  drilldata.setFormattedValue(i, 0, key + ' (' + (val/total * 100).toFixed(1) + '%)');                  
                                });

                                var optionsdrill = {
                                    backgroundColor: "#1e202c",
                                    //legend: { position: 'labeled' },
                                    legend: { position: 'right', },
                                    tooltip: { text: 'none' },
                                    pieSliceText: 'none',
                                    sliceVisibilityThreshold: 0,
                                    //pieStartAngle: 90,
                                    //width: 650,
                                    //height: 500,
                                    //fontSize: 11,
                                    //chartArea:{left:0,right: 0,}
                                    chartArea: {
                                      bottom: 24,
                                      height: '100%',
                                      left: 10,
                                      right: 0,
                                      top: 24,
                                      width: '100%'
                                    },
                                  //title: 'Activity Cost',
                                  //width: 450, height: 450,
                                };

                                $('.drilldownchart1').hide();
                                $('.drilldown2chart1').show('slide', {direction: 'right'}, 1000);

                                var chartdrill = new google.visualization.PieChart(document.getElementById('activitywise-cost')); 

                                chartdrill.draw(drilldata, optionsdrill);

                                $('#iowheadname').html(data3.iowhead);

                                $('#activity-names-list').html(data3.activitylist);

                            }

                          }
                        });
                      }
                    }

                    google.visualization.events.addListener(chart, 'select', selectHandler);

                    chart.draw(data, options);

                    $('#iow-names-list').html(data2.iowlist);

                }

                /*if(data2.iowcount>0){

                    google.charts.load('current', {'packages':['corechart']});
                    google.charts.setOnLoadCallback(drawChart2);

                    function drawChart2() {

                        var count = data2.iowcount - 1;

                        for (var k=0;k<=count;k++){

                            var data = new google.visualization.DataTable();
                            data.addColumn({ type: 'string', id: 'Role' });
                            data.addColumn('number', '');

                            eval(data2.actdatarows[k]);

                            var datarowscount = data.getNumberOfRows();

                            if(datarowscount>0){

                                $('#activitywisechart').append("<div class='col-md-4'><div class='card chart-card'><div class='card-header'>"+data2.iownames[k]+"<span class='icon-arrow-left-thick mainlistback'></span></div><div class='card-body iow-cost' id='iow-cost"+k+"' style='width: 400px; height: 300px;'></div></div></div>");

                                var options = {
                                  //title: 'Activity Cost',
                                  //width: 450, height: 450,
                                };

                                var chart = new google.visualization.PieChart(document.getElementById('iow-cost' + k));

                                chart.draw(data, options);

                            }

                        }

                    }

                }*/

            }
            else{
                $('#errormessage').html(data2.result);
            }

            $('.preloader').hide();

        }

    });

}


function getprjctdriverscostchart()
{

    $.ajax({ 

        type: 'POST',

        url: '../dashboard/projectdrivercost',

        beforeSend : function(){

            $('.preloader').show();

        },

        dataType: "json",

        data: {},

        success: function(data2){

            if(data2.error=='No')

            {
                //$("#prj_name").html('Project Dashboard - '+data2.project); 
                
                google.charts.load('current', {'packages':['corechart']});
                google.charts.setOnLoadCallback(drawChart);

                function drawChart() {

                    var data = new google.visualization.DataTable();
                    data.addColumn({ type: 'string', id: 'Role' });
                    data.addColumn('number', '');

                    eval(data2.result);

                    /*var data = google.visualization.arrayToDataTable([
                      ['Task', 'Hours per Day'],
                      ['Work',     1000],
                      ['Eat',      20]
                    ]);*/

                    var options = {
                        backgroundColor: "#1e202c",
                        legend: { position: 'bottom' },
                      //title: 'Activity Cost',
                      //width: 450, height: 450,
                    };

                    var chart = new google.visualization.PieChart(document.getElementById('prjctdrivers-cost'));



                    function selectHandler() {
                      var selectedItem = chart.getSelection()[0];
                      if (selectedItem) {
                        var topping = data.getValue(selectedItem.row, 0);
                        //alert(topping);
                        $.ajax({
                          type: 'POST',
                          url: '../dashboard/projectrestypedrivercost',
                          dataType: "json",
                          data: {resourcetypename:topping},
                          success: function(data3){

                            google.charts.load('current', {'packages':['corechart']});
                            google.charts.setOnLoadCallback(drawChartdrill);

                            function drawChartdrill() {

                                var drilldata = new google.visualization.DataTable();
                                drilldata.addColumn({ type: 'string', id: 'Role' });
                                drilldata.addColumn('number', '');

                                eval(data3.result);

                                var count  = drilldata.getNumberOfRows();
                                var values = Array(count).fill().map(function(v, i) {
                                  return drilldata.getValue(i, 1);
                                });
                                var total =  google.visualization.data.sum(values);
                                values.forEach(function(v, i) {                                         
                                  var key = drilldata.getValue(i, 0);
                                  var val = drilldata.getValue(i, 1);
                                  drilldata.setFormattedValue(i, 0, key + ' (' + (val/total * 100).toFixed(1) + '%)');                  
                                });

                                //drilldata.sort([{column: 1}]);

                                var optionsdrill = {
                                    backgroundColor: "#1e202c",
                                    legend: { position: 'right' },
                                    tooltip: { text: 'none' },
                                    pieSliceText: 'none',
                                    sliceVisibilityThreshold: 0,
                                    //pieStartAngle: 90,
                                    chartArea: {
                                      bottom: 24,
                                      height: '100%',
                                      left: 10,
                                      right: 0,
                                      top: 24,
                                      width: '100%'
                                    },
                                  //title: 'Activity Cost',
                                  //width: 450, height: 450,
                                };

                                $('.mainlist').hide();
                                $('.drilldownchart3').show('slide', {direction: 'right'}, 1000);

                                var chartdrill = new google.visualization.PieChart(document.getElementById('drivers-cost')); 


                                 chartdrill.draw(drilldata, optionsdrill);

                                $('#restypenames').html(data3.driversname);

                                $('#driverscost-list').html(data3.resourcelists);

                            }

                          }
                        });
                      }
                    }

                    google.visualization.events.addListener(chart, 'select', selectHandler);



                    chart.draw(data, options);
                }

            }
            else{
                $('#errormessage').html(data2.result);
            }

            $('.preloader').hide();

        }

    });

}

function getprjctcompletedatechart()
{

    $.ajax({ 

        type: 'POST',

        url: '../dashboard/projectcompletedate',

        beforeSend : function(){

            $('.preloader').show();

        },

        dataType: "json",

        data: {},

        success: function(data2){

            if(data2.error=='No')

            {
                
                $('#prjct-complete').html(data2.result);

            }
            else{
                $('#errormessage').html(data2.result);
            }

            $('.preloader').hide();

        }

    });

}

function getcapctyutiliztnchart(){

  $.ajax({
      type: 'POST',
      url: '../dashboard/capacityutiztn',
      dataType: "json",
      data: {},
      success: function(data2){

        google.charts.load('current', {packages: ['corechart', 'bar']});
        google.charts.setOnLoadCallback(drawMultSeries);

        function drawMultSeries() {
            /*var data = google.visualization.arrayToDataTable([
              ['Name', '', '',{type:'string', role:'style'}],
              ['Sub:Contractors', 100, 80, 'green'],
              ['Direct Labour', 100, 120, 'red'],
              ['Plant & Equipment  Usage', 100, 70, 'green'],
              ['Leased Equipment/Premises', 100, 140, 'red']
            ]);*/

            var data = new google.visualization.DataTable();

            data.addColumn({ type: 'string', id: 'Name' });
            data.addColumn('number', '');
            data.addColumn('number', '');
            data.addColumn({type:'string', role:'style'});

            eval(data2.result);

            var options = {
              //title: 'Population of Largest U.S. Cities',
              //width: 370, height: 380,
              backgroundColor: '#1e202c',
              legend: { position: 'none'},
              //chartArea: {width: '50%'},
              chartArea: {
                right: 25,
                //left:0,
              },
              hAxis: {
                title: 'Percentage',
                titleTextStyle: {
                    color: 'white'
                },
                gridlines: {color: '#252c3e' },
                textStyle: {
                  color: 'white'
                },
                format: "#'%'",
                minValue: 0
              },
              vAxis: {
                title: 'Resource Type',
                textStyle: {
                  color: 'white'
                }
              }
            };

            var chart = new google.visualization.BarChart(document.getElementById('capactyutz_chart'));
            chart.draw(data, options);

        }

      }
  });

}

$(document).on('click','.capactyutz_chart',function(){

    $('.mainlist').hide();
    $('.drilldownchart5').show('slide', {direction: 'right'}, 1000);

    getcpctyresource();

});

function getcpctyresource()
{

    $.ajax({
        type: 'POST',
        url: '../dashboard/capacityutiztnresource',
        dataType: "json",
        data: {},
        success: function(data2){

        if(data2.count>0){

          google.charts.load('current', {packages: ['corechart', 'bar']});
          google.charts.setOnLoadCallback(drawChart);

          function drawChart() {

            var count = data2.count - 1;

            for (var k=0;k<=count;k++){
              
              var data = new google.visualization.DataTable();

              data.addColumn({ type: 'string', id: 'Name' });
              data.addColumn('number', '');
              data.addColumn('number', '');
              data.addColumn({type:'string', role:'style'});

              eval(data2.result[k]);

              var rowcount = data.getNumberOfRows();

              if(rowcount==4){
                var barwidth = '30%';
              }
              else if(rowcount==3){
                var barwidth = '30%';
              }
              else if(rowcount==2){
                var barwidth = '20%';
              }
              else if(rowcount==1){
                var barwidth = '10%';
              }
              else{
                var barwidth = 0;
              }

              if(barwidth == 0){

                var options = {
                  //title: 'Population of Largest U.S. Cities',
                  //width: 370, height: 380,
                  backgroundColor: '#1e202c',
                  legend: { position: 'none'},
                  //chartArea: {width: '50%'},
                  chartArea: {
                    right: 25,
                    //left:0,
                  },
                  hAxis: {
                    title: 'Percentage',
                    titleTextStyle: {
                        color: 'white'
                    },
                    gridlines: {color: '#252c3e' },
                    textStyle: {
                      color: 'white'
                    },
                    format: "#'%'",
                    minValue: 0
                  },
                  vAxis: {
                    title: 'Resource Type',
                    textStyle: {
                      color: 'white'
                    }
                  }
                };

              }
              else{

                var options = {
                  //title: 'Population of Largest U.S. Cities',
                  //width: 370, height: 380,
                  backgroundColor: '#1e202c',
                  legend: { position: 'none'},
                  //chartArea: {width: '50%'},
                  chartArea: {
                    right: 25,
                    //left:0,
                  },
                  bar: { groupWidth: barwidth },
                  hAxis: {
                    title: 'Percentage',
                    titleTextStyle: {
                        color: 'white'
                    },
                    gridlines: {color: '#252c3e' },
                    textStyle: {
                      color: 'white'
                    },
                    format: "#'%'",
                    minValue: 0
                  },
                  vAxis: {
                    title: 'Resource Type',
                    textStyle: {
                      color: 'white'
                    }
                  }
                };

              }

              var chart = new google.visualization.BarChart(document.getElementById('capactyutzres_chart' + k));
              chart.draw(data, options);

            }

          }

        }

      }

    });

}

function getprojectroichart(){
  $.ajax({
      type: 'POST',
      url: '../dashboard/roichart',
      dataType: "json",
      data: {},
      success: function(data2){
        google.charts.load("current", {packages:['corechart']});
        google.charts.setOnLoadCallback(drawChart);
        function drawChart() {

          var data = new google.visualization.DataTable();

          data.addColumn({ type: 'string', id: 'Role' });
          data.addColumn('number', '');
          data.addColumn('number', '');
          data.addColumn({type:'string', role:'style'});
          //data.addColumn({type:'number', role:'annotation'});
          
          eval(data2.result);

          var options = {
            backgroundColor: '#1e202c',
            legend: { position: 'none'},
            annotations: {
                 textStyle: {
                     color: 'white',
                     fontSize: 11,
                 },
                 alwaysOutside: true
            },
            //bar: { groupWidth: '20%' },
            isStacked: false,
            vAxis: {
              title: 'Percentage',
              titleTextStyle: {
                  color: 'white'
              },
              gridlines: {color: '#252c3e' },
              textStyle: {
                color: 'white'
              },
              format: "#'%'",
              /*viewWindow: {
                  min: 70,
                  max: 150
              },*/
            },
            animation: {startup:true,duration: 1000,easing: 'out'},
            //colors: [data2.color1, data2.color2]
            //colors: ['#0099c6', '#109618']
          };

          var chart = new google.visualization.SteppedAreaChart(document.getElementById("roi_chart"));
          chart.draw(data, options);

      }

    }

  });
}