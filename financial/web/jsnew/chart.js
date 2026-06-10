/**
 * Created by SolmindsDelli5 on 26-09-2019.
 */
$(document).on( "click", ".chart", function(){
    //$('.acc_container').slideUp();
    $('#project').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
    //$(this).toggleClass('active').next().slideDown();
    $('#chartitems').addClass('active').next('.acc_container').slideDown();
    var id= $(this).val();
    $('#selectedProjectId').val(id);
    
    $.ajax({

        type: 'POST',

        //url: '../projects1/Chartdetails',
        url: '../projects1/Iowcosting',
        beforeSend : function(){

            $('.preloader').show();

        },

        dataType: "json",

        data: {project:$('#selectedProjectId').val()},

        success: function(data){

            if(data.error=='No')

            {
                //getdialchart();
                //getProjectexpenditure();
                $('.preloader').hide();
                $('#dashpetable').show();
                $('#dashpeitems').html(data.result);

                var actualamt=data.actual_amt;
                var estcostamt=data.estimate_amt;
                //console.log(estcostamt);
                var actualstr=data.act_numstr;

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
});

$(document).on('click','.chartitemsofwork',function(){
    $('#projestdashsection').hide('slide', {direction: 'left'}, 1000);
    $('#projestiowdashsection').show('slide', {direction: 'right'}, 1000);
    //$('#accountspiesection').show();
    var id=$(this).attr('data-id');
    var estiowid=$('#itemofworkid'+id).val();
    $.ajax({

        type: 'POST',

        url: '../projects1/ItemsofworkCost',
        beforeSend : function(){

            $('.preloader').show();

        },

        dataType: "json",

        data: {estiowid:estiowid,project:$('#selectedProjectId').val()},

        success: function(data){

            if(data.error=='No')

            {
                $('.preloader').hide();
                $('#dashestiowtable').show();
                $('#dashestiowitems').html(data.result);
                $('#estiowdashinfo').html(data.iowname);
                getestiowdialchart($('#selectedProjectId').val(),estiowid);
                google.charts.load('current', {'packages':['corechart']});
                google.charts.setOnLoadCallback(drawChart);
                function drawChart() {

                    eval(data.itemsofwork);

                    var options = {
                        //title: 'Statement of Project',
                        is3D:false,
                        //legend: 'none',
                        pieSliceText: 'percentage'
                        //slices: {  9: {offset: 0.2}},
                        //}
                        //tooltip: { trigger: 'none' }
                    };

                    var chart = new google.visualization.PieChart(document.getElementById('piechartestiow'));

                    chart.draw(data, options);
                }

            }

        }

    });
});

function getProjectexpenditure()
{
    $.ajax({

        type: 'POST',

        url: '../Projects1/Piechart',

        dataType: "json",

        data: {project:$('#selectedProjectId').val()},

        success: function(data){

            if(data.error=='No')

            {
                google.charts.load('current', {'packages':['corechart']});
                google.charts.setOnLoadCallback(drawChart);
                function drawChart() {
                    
                    eval(data.result);
                    var options = {
                        //title: 'Statement of Project',
                        is3D:false,
                        //legend: 'none',
                        pieSliceText: 'percentage',
                    };
                    var chart = new google.visualization.PieChart(document.getElementById('piechart'));
                    chart.draw(data, options);
                 
                }
            }
        }
    });
}

function getdialchart()
{
    $.ajax({

        type: 'POST',

        url: '../Projects1/Dialgaugecal',
        beforeSend : function(){

            $('.preloader').show();

        },

        dataType: "json",

        data: {project:$('#selectedProjectId').val()},

        success: function(data){

            if(data.error=='No')

            {             
                google.charts.load('current', {
                  packages: ['gauge']
                }).then(function () {
                  var dataHumid = google.visualization.arrayToDataTable([
                    ['Label', 'Value'],
                    ['', 0]
                  ]);

                  var optionsHumid = {
                    min: 0, max: 200, 
                    greenFrom: 0, greenTo: data.green, 
                    yellowFrom: data.green, yellowTo: data.yellow, 
                    redFrom: 100, redTo: 200, 
                    majorTicks: ['0','25','50','75','100','125','150','175','200'],
                    minorTicks: 5
                  };

                  var formatHumid = new google.visualization.NumberFormat({
                    suffix: '%',
                    fractionDigits: 0
                  });
                  formatHumid.format(dataHumid, 1);

                  var chartHumid = new google.visualization.Gauge(document.getElementById("gauge_div"));

                  chartHumid.draw(dataHumid, optionsHumid);

                  setInterval(function() {
                    dataHumid.setValue(0, 1, data.result);
                    formatHumid.format(dataHumid, 1);
                    chartHumid.draw(dataHumid, optionsHumid);
                  }, 1300);

                });
            }

        }

    });
}

function getestiowdialchart(projectid,estiowid)
{
    $.ajax({

        type: 'POST',

        url: '../Projects1/Itemsofworkdial',

        dataType: "json",

        data: {estiowid:estiowid,project:projectid},

        success: function(data){

            if(data.error=='No')

            {
                google.charts.load('current', {
                    packages: ['gauge']
                }).then(function () {
                    var dataHumid = google.visualization.arrayToDataTable([
                        ['Label', 'Value'],
                        ['', 0]
                    ]);

                    var optionsHumid = {
                        min: 0, max: 200,
                        greenFrom: 0, greenTo: data.green,
                        yellowFrom: data.green, yellowTo: data.yellow,
                        redFrom: 100, redTo: 200,
                        majorTicks: ['0','25','50','75','100','125','150','175','200'],
                        minorTicks: 5
                    };

                    var formatHumid = new google.visualization.NumberFormat({
                        suffix: '%',
                        fractionDigits: 0
                    });
                    formatHumid.format(dataHumid, 1);

                    var chartHumid = new google.visualization.Gauge(document.getElementById("gaugeestiow_div"));

                    chartHumid.draw(dataHumid, optionsHumid);

                    setInterval(function() {
                        dataHumid.setValue(0, 1, data.result);
                        formatHumid.format(dataHumid, 1);
                        chartHumid.draw(dataHumid, optionsHumid);
                    }, 1300);

                });
            }

        }

    });
}
