$(document).on('click','.navbar-nav .proc-dashboard', function(e){
        e.preventDefault();
        if($('.overNow').hasClass('active')){
            $( ".overNow" ).removeClass("active");
            $('.menu-popup-cntnr').removeClass('active');
            $('body').css('overflow-y','auto');
        }else if($('.overNow1').hasClass('active')){
            $( ".overNow1" ).removeClass("active");
            $('.menu1-popup-cntnr').removeClass('active');
            $('body').css('overflow-y','auto');
        }else if($('.overNow2').hasClass('active')){
            $( ".overNow2" ).removeClass("active");
            $('.finmenu-popup-cntnr').removeClass('active');
            $('body').css('overflow-y','auto');
        }else if($('.overNow4').hasClass('active')){
            $( ".overNow4" ).removeClass("active");
            $('.menu4-popup-cntnr').removeClass('active');
            $('body').css('overflow-y','auto');
        }
        $(this).toggleClass('active');
        if($(this).hasClass('active')){
            $('#procurement-title-head').html('Procurement Dashboard');
            $('.chart-popup-cntnr').addClass('active');
            $('body').css('overflow-y','hidden');
            $('#listprocdasboard').trigger('click');
        }else{
            //return true;
            $('#procurement-title-head').html('Procurement Process');
            $('.chart-popup-cntnr').removeClass('active');
            $('body').css('overflow-y','auto');
        }
        
});

$(document).on('click','.procdash-close', function(e){
    $('#procurement-title-head').html('Procurement Process');
    $('.chart-popup-cntnr').removeClass('active');
    $('body').css('overflow-y','auto');
    
});

$(document).on('click','#restyp-materials', function(e){
    $('.mainlist').hide();
    $('.drilldownlist1').show();
    getprocurementresourcechart();

});

$(document).on( "click", ".mainproclistback", function(){

    //$('.mainlist').hide('slide', {direction: 'left'}, 1000);
    $('.drilldownlist1').hide();
    $('.mainlist').show('slide', {direction: 'left'}, 1000);

});

$(document).on( "click", ".mainproclistback1", function(){

    //$('.mainlist').hide('slide', {direction: 'left'}, 1000);
    $('.drilldownlist2').hide();
    $('.drilldownlist1').show();   

});

$(function(){

    $('#listprocdasboard').click(function(){ 

        $('.mainlist').show();
        $('.drilldownlist1').hide();
        $('.drilldownlist2').hide();

        getprocurementchart();

    });

});


function getprocurementchart()
{

  $.ajax({
      type: 'POST',
      url: '../dashboard/procurementchart',
      dataType: "json",
      data: {},
      success: function(data2){
        google.charts.load("current", {packages:['corechart']});
        google.charts.setOnLoadCallback(drawChart);
        function drawChart() {

            var data = new google.visualization.DataTable();

            data.addColumn('string', 'Name');
            data.addColumn('number', 'Amount');
            data.addColumn({type:'number', role:'annotation'});
          
            eval(data2.result);

            var maxval = data.getColumnRange(1).max + 10;

            var options = {
                backgroundColor: '#1e202c',
                chartArea: {
                    right: 25,
                    //left:0,
                },
                //legend: { position: 'bottom', alignment: 'end',textStyle: {color: 'white'} },
                legend: { position: 'none'},
                annotations: {
                     textStyle: {
                         color: 'white',
                         fontSize: 11,
                     },
                     alwaysOutside: true
                },
                bar: { groupWidth: '30%' },
                //isStacked: true,
                vAxis: {
                    title: data2.numstr,
                    gridlines: {color: '#252c3e' },
                    textStyle: {
                        color: 'white'
                    },
                    viewWindow: {
                      max: maxval
                    },
                },
                //colors: [data2.color1, data2.color2]
                colors: ['orange', 'green', 'yellow']
            };

            var chart = new google.visualization.ColumnChart(document.getElementById("restyp-materials"));
            chart.draw(data, options);

      }

    }

  });

}

function getprocurementresourcechart()
{

  $.ajax({
      type: 'POST',
      url: '../dashboard/procurementresourcechart',
      dataType: "json",
      data: {},
      success: function(data2){

        google.charts.load("current", {packages:['corechart']});
        google.charts.setOnLoadCallback(drawChart);
        function drawChart() 
        {

            var data = new google.visualization.DataTable();

            data.addColumn('string', 'Name');
            data.addColumn('number', 'Amount');
            data.addColumn({type:'string', role:'style'});
            data.addColumn('number', 'Amount');
            data.addColumn({type:'string', role:'style'});
            data.addColumn({type:'number', role:'annotation'});
          
            eval(data2.result);

            /*var data = google.visualization.arrayToDataTable([
                ['Genre', 'Amount1', { role: "style" }, 'Amount2', { role: "style" }, { role: 'annotation' } ],
                ['2010', 10, 'green', 0, '', ''],
                ['2020', 10, 'green', 10, 'yellow', ''],
              ]);*/

            var maxval = data.getColumnRange(1).max + 10;

            var options = {
                backgroundColor: '#1e202c',
                isStacked: true,
                /*chartArea: {
                    right: 25,
                    //left:0,
                },*/
                //legend: { position: 'bottom', alignment: 'end',textStyle: {color: 'white'} },
                legend: { position: 'none'},
                annotations: {
                     textStyle: {
                         color: 'white',
                         fontSize: 11,
                     },
                     alwaysOutside: true
                },
                bar: { groupWidth: '30%' },
                //isStacked: true,
                vAxis: {
                    title: data2.numstr,
                    gridlines: {color: '#252c3e' },
                    textStyle: {
                        color: 'white'
                    },
                    viewWindow: {
                      max: maxval
                    },
                },
                //colors: [data2.color1, data2.color2]
                //colors: ['blue']
            };

            var chart = new google.visualization.ColumnChart(document.getElementById("restyp-resource"));
            chart.draw(data, options);

        }

    }

  });

}

$(document).on('click','.resourcelisting', function(e){
    var id = $(this).attr('data-id');
    var resourcename = $('#resourname'+id).html();

    $.ajax({
        type: 'POST',
        url: '../dashboard/procresourcedrill',
        dataType: "json",
        data: {resourcename:resourcename},
        success: function(data2)
        {
            $('.drilldownlist1').hide();
            $('.drilldownlist2').show();

            google.charts.load("current", {packages:['corechart']});
            google.charts.setOnLoadCallback(drawChart);
            function drawChart() 
            {

                var data = new google.visualization.DataTable();

                data.addColumn('string', 'Name');
                data.addColumn('number', 'Amount');
                data.addColumn({type:'string', role:'style'});
                /*data.addColumn('number', 'Amount');
                data.addColumn({type:'string', role:'style'});*/
                data.addColumn({type:'number', role:'annotation'});
              
                eval(data2.result);

                var maxval = data.getColumnRange(1).max + 10;

                var options = {
                    backgroundColor: '#1e202c',
                    /*isStacked: true,
                    chartArea: {
                        right: 25,
                        //left:0,
                    },*/
                    //legend: { position: 'bottom', alignment: 'end',textStyle: {color: 'white'} },
                    legend: { position: 'none'},
                    annotations: {
                         textStyle: {
                             color: 'white',
                             fontSize: 11,
                         },
                         alwaysOutside: true
                    },
                    bar: { groupWidth: '30%' },
                    //isStacked: true,
                    vAxis: {
                        title: 'Quantity',
                        gridlines: {color: '#252c3e' },
                        textStyle: {
                            color: 'white'
                        },
                        viewWindow: {
                          max: maxval
                        },
                    },
                    //colors: [data2.color1, data2.color2]
                    //colors: ['blue']
                };

                var chart = new google.visualization.ColumnChart(document.getElementById("resource-barchart"));
                chart.draw(data, options);

            }

            if(data2.activitycount>0)
            {
                $('#resactvtydial').html('');
                google.charts.load('current', {'packages':['gauge']});
                google.charts.setOnLoadCallback(drawChart);

                function drawChart() {

                    var count = data2.activitycount - 1;

                    for (var k=0;k<=count;k++){
                      //$('#iowyieldchart').append("<div class='col-md-4'><div><b>IOW 1</b></div><div class='chart_3drill'><div id='iowyieldchartlist+"k"' class='iowyieldchartlist'></div></div></div>");
                      $('#resactvtydial').append("<div class='col-md-4 space-between'><div class='chart-headings' style='height: 80px;'><b style=''>"+data2.activitynames[k]+"</b></div><div class='twocol-cntnr'><div class='chartDetails-chart-wrpr'><div class='chart-cntnr'><div class='gauge_chart' style='display: flex;align-items: center;'><div class='activityconsudialchart' id='activityconsudialchart"+k+"' style='width: 400px; height: 350px;'></div></div></div></div></div></div>");
                        
                        var actualamt=data2.act_consumptn[k];
                        var estcostamt=data2.est_consumptn[k];
                        var actual = actualamt * 1;
                        var data = google.visualization.arrayToDataTable([
                          ['Label', 'Value'],
                          ['', actual]
                        ]);
                        //var estcost = estcostamt / 4;
                        if(estcostamt == 0)
                        {
                            var estcost = (60 * 1) / 6;
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
                                //yellowFrom:0, yellowTo: majtricks[6],
                                //majorTicks: ['0', '25', '50', '75', '100', '125', '150', '175', '200'],
                                majorTicks: majtricks,
                                //minorTicks: 6
                            };

                        }
                        else
                        {
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
                                //minorTicks: 6
                            };

                        }

                        var chart = new google.visualization.Gauge(document.getElementById('activityconsudialchart' + k));
                        chart.draw(data, options);

                    }

                }

            }

        }

    });
    
});