<!DOCTYPE html>
<html lang="en" class="no-js">

<!-- Head -->
<head>
  <style>
#A {
  margin: auto;
  width: 90%;
  /* border: 3px solid rgb(153, 26, 30); */
  padding: 10px;
    /* position: fixed; */
    /* background:orange; */
    display:none;
}
#B {
  margin: auto;
  /* width: 90%; */
  /* border: 3px solid rgb(153, 26, 30); */
  padding: 10px;
  /* background:green; */
}
#embedded-Gantt
{
  border: 2px solid rgb(0, 0, 0);
  overflow: hidden;
  width: 100%;
}
.schedule-edit-table{
  border: 1px solid #b6b6b6;
  border-bottom: 1px solid #b6b6b6;
  overflow: hidden;
  background-color: #fff;
}
.schedule-edit-table th
{
  border-bottom: 1px solid #b6b6b6 !important;
  border-top: 1px solid #b6b6b6 !important;
  border-right: 1px solid #b6b6b6;
}
.schedule-edit-table td
{
  border-top: 1px solid #b6b6b6 !important;
  border-right: 1px solid #b6b6b6;
}

  </style>
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/jsgantt.css" />

    <link rel="stylesheet" type="text/css" href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/jsgantt-extra.css" />
   
    <!--<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous" />-->
   
  <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Cookie|Satisfy|Kelly+Slab|Overlock" rel="stylesheet">
    
    <!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>-->
</head>
<body>
<!-- -->

 <!--  -->

 <div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
    
 <div id="A">
    <form id="activities_edit_form">
      <button class="btn" type="button" id="close-edit" style="position: relative;
      width: 100px;
      float: right;
      margin-bottom: 3px;
      background: #072c47;
      border-radius: 20px;
      margin-left: 15px;
      display: none;
      color: #fff">Close</button>
      <button class="btn" type="button" id="submit_form" style="position: relative;
      width: 100px;
      float: right;
      margin-bottom: 3px;
      background: #072c47;
      border-radius: 20px;
      display: none;
      color: #fff">Save</button>
      <table class="table schedule-edit-table" style="text-align: left; display: none;">
          <thead>
            <tr>
              <th>Activities</th>
              <!-- <th>Remaining quantity</th> -->
              <th>Duration</th>
              <th>Budgeted Start Date</th>
              <th>Budgeted End Date</th>
              <!-- <th>Lag (days)</th> -->
              <th>Actual Start Date</th>
              <!--<th>Actual End Date</th> -->
            </tr>
          </thead>
          <tbody id="ganttchartitems" class="ui-sortable">
            <?php echo $activities; ?>
          </tbody>
        </table>
    </form>

 </div>

    <!--  -->
<div id="B">
    <button class="btn edit_toggle" style="position: relative;width:100px;float: right;
    margin-bottom: 3px;
    background: #072c47;
    border-radius: 20px; 
    display: none;
    color: #fff;">
    Edit
    </button>
   <button class="btn edit_relation" style="position: relative;width:100px;float: right;
    margin-bottom: 3px;
    background: #072c47;
    border-radius: 20px; 
    display: none;
    color: #fff;margin-right:20px;">Relations</button>
    <div id="embedded-Gantt"></div>
  </div>
<!-- -->
<div id="C" style="display:none;">
    
    <button class="btn" type="button" id="close" style="position: relative;
    width: 100px;
    float: right;
    margin-bottom: 3px;
    background: #072c47;
    border-radius: 20px;
    display: none;
    color: #fff">Close</button>
     <form>
        <table class="table schedule-edit-table" style="text-align: left;display: none;">
            <thead>
              <tr>
                <th>Precedent Item</th>
               <!-- <th>Schedule Item</th>
                <th>Remaining quantity</th> -->
                <th>Precedent Activity</th>
                <th>Dependent Item</th>
                <th>Dependent Activity</th>
                <th>Relation</th>
                <th>Lag/Lead (days)</th>
                <th></th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php echo $relationsHtml; ?>
              
            </tbody>
        </table>
      </form>

</div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="width: 617px; height: 500px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="margin-left: 565px;">&times;</button>
                 <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body">
                <div id="columnchart_values" style="width: 900px; height: 300px;"></div>
            </div>
        </div>
    </div>
</div>

<script language="javascript" src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/projectsmain/jsgantt.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>

    <!-- Font Awesome -->
<script src="https://use.fontawesome.com/78d1e57168.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <!-- Google's Code Prettify -->
<script src="https://cdn.rawgit.com/google/code-prettify/master/loader/run_prettify.js?lang=css&amp;skin=sunburst"></script>
<script type="text/javascript">

function loadGantt()
{
  var g = new JSGantt.GanttChart(document.getElementById('embedded-Gantt'), 'month');
  if (g.getDivId() != null) {

    /*vAdditionalHeaders = {
      category: {
        title: 'B.Qty'
      },
    }*/

    //g.setOptions({ vAdditionalHeaders, });

    g.setUseSort(0);
    g.setUseToolTip(0);
    g.setCaptionType('Complete');  // Set to Show Caption (None,Caption,Resource,Duration,Complete)
    g.setQuarterColWidth(50);
    //g.setRowHeight(100);
    g.setShowDur(0);
    g.setShowStartDate(1);
    g.setShowEndDate(1);
    g.setShowPlanStartDate(1);
    // g.setShowTaskInfoStartDate(1);
    // g.setShowTaskInfoEndDate(1);
    g.setShowPlanEndDate(0);
    // g.setDayColWidth(21);
    g.setDateTaskDisplayFormat('day dd month yyyy'); // Shown in tool tip box
    g.setDayMajorDateDisplayFormat('mon yyyy - Week ww') // Set format to display dates in the "Major" header of the "Day" view
    g.setWeekMinorDateDisplayFormat('dd mon') // Set format to display dates in the "Minor" header of the "Week" view
    g.setShowTaskInfoLink(1); // Show link in tool tip (0/1)
    g.setShowComp(0);
    g.setShowRes(1);
    g.setShowDeps(1);
    g.setShowEndWeekDate(0); // Show/Hide the date for the last day of the week in header for daily view (1/0)
    g.setUseSingleCell(10000); // Set the threshold at which we will only use one cell per table row (0 disables).  Helps with rendering performance for large charts.
    //g.setFormatArr('Day', 'Week', 'Month', 'Quarter'); // Even with setUseSingleCell using Hour format on such a large chart can cause issues in some browsers
    g.setFormatArr('Week', 'Month');
    var criticalid = [];
    $.ajax({
        type: 'POST',
        url: '../projectsmain/getstructureactivites',
        dataType: "json",
        async:false,
        data: {projectid:<?= $projectId ?>},
        success: function(data1){
          // var items=JSON.parse(data.result);
            // console.log(data1.result);
            // alert(data.error);
            var full_array = [];
            var activity_id = '';
            for (j = 0; j < data1.result.length; j++) {
              var activity = data1.result[j];
              var random = 'ABC'+activity.id;

              var startDate = Date.parse(activity.start_date);
              var endDate = Date.parse(activity.end_date);
              var timeDiff = endDate - startDate;
              daysDiff = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
              budgetedDayDifference = daysDiff+1;

              var actualStartDate = Date.parse(activity.actual_start_date);
              console.log('real'+activity.actual_end_date);
              if(activity.actual_end_date != null)
              {
                var actualEndDate = Date.parse(activity.actual_end_date);

              }
              else
              {
                // alert()
                var myDate = new Date(activity.actual_start_date);
                myDate.setDate(myDate.getDate()+(activity.duration-1));
                var day = myDate.getDate();
                var month = myDate.getMonth() + 1;
                var year = myDate.getFullYear();
                if (day < 10) {
                    day = "0" + day;
                }
                if (month < 10) {
                    month = "0" + month;
                // var date = day + "/" + month + "/" + year;
                }
                var actualEndDateString = year+'-'+month+'-'+day;
                var actualEndDate =  Date.parse(actualEndDateString); 
                // alert(actualEndDate);
              }
              var actualtimeDiff = actualEndDate - actualStartDate;
              actualdaysDiff = Math.floor(actualtimeDiff / (1000 * 60 * 60 * 24));
              actualDayDifference = actualdaysDiff+1;
              // alert(actualDayDifference);

              var totalQuantity = activity.quantity;
              var completedQuantity = activity.completedQuantity;
              // alert(completedQuantity);
              if(totalQuantity == completedQuantity || completedQuantity == null || completedQuantity == 0 )
              {
                // var percentageDifference = (actualDayDifference/budgetedDayDifference)*100;
                // console.log(completedQuantity);
                if(completedQuantity ==null)
                  finalActEndDate = actualEndDateString;
                else
                  finalActEndDate = activity.actual_end_date;

                // alert(finalActEndDate);
              }
              else
              {
                var dayTakenFOrOneUnitQuantity = actualDayDifference/completedQuantity;
                var totalDaysForCompletion = dayTakenFOrOneUnitQuantity*totalQuantity;
                // var finalActEndDate ;
                var myDate = new Date(activity.actual_start_date);
                myDate.setDate(myDate.getDate()+(totalDaysForCompletion-1));
                console.log(myDate);
                // formating date
                var day = myDate.getDate();
                var month = myDate.getMonth() + 1;
                var year = myDate.getFullYear();
                if (day < 10) {
                    day = "0" + day;
                }
                if (month < 10) {
                    month = "0" + month;
                }
                // var date = day + "/" + month + "/" + year;

                finalActEndDate = year+'-'+month+'-'+day;

                // var percentageDifference = (totalDaysForCompletion/budgetedDayDifference)*100;
              }
              console.log("activity.actual_start_date= "+activity.actual_start_date);
              console.log("activity.actual_end_date= "+activity.actual_end_date);
              console.log("completedQuantity= "+completedQuantity);
              console.log("finalActEndDate= "+finalActEndDate);
              // alert(finalActEndDate);
              endDateString = new Date(activity.end_date);
              actualEndDateString = new Date(finalActEndDate);

              if(actualEndDateString > endDateString)
                $class = 'gtaskred';
              else
                $class = 'gtaskblue';

              var today = new Date();
              var dd = today.getDate();
              var mm = today.getMonth() + 1;

              var yyyy = today.getFullYear();
              if (dd < 10) {
                dd = '0' + dd;
              } 
              if (mm < 10) {
                mm = '0' + mm;
              } 
              var today = yyyy + '/' + mm + '/' + dd;

              var budgeted_start_date = activity.start_date;
              if(budgeted_start_date==null){ BudgetedStartDate = today; }
              else if(budgeted_start_date=='0000-00-00'){ BudgetedStartDate = today; }
              else{ BudgetedStartDate = activity.start_date; }                         

              var budgeted_end_date = activity.end_date;
              if(budgeted_end_date==null){ BudgetedEndDate = today; }
              else if(budgeted_end_date=='0000-00-00'){ BudgetedEndDate = today; }
              else{ BudgetedEndDate = activity.end_date; } 

              var actual_start_date = activity.actual_start_date;                         
              if(actual_start_date==null){ finalActualStartDate = today; }
              else if(actual_start_date=='0000-00-00'){ finalActualStartDate = today; }
              else{ finalActualStartDate = activity.actual_start_date; }

              if(finalActEndDate==null){ finalActualEndDate = today; }
              else if(finalActEndDate=='0000-00-00'){ finalActualEndDate = today; }
              else{ finalActualEndDate = finalActEndDate;}

              var percentagecompleted = (completedQuantity/totalQuantity)*100;
              var finalpercentagecompleted = (percentagecompleted).toFixed(2);

              //full_array.push('id_'+j+': '+activity.id, j+'_'+finalActualStartDate , j+'_'+finalActualEndDate);

              //full_array1.push(new Date(finalActualEndDate));
              //alert(activity.id)

              //full_array.push({ 'key': activity.id, 'value': finalActualEndDate});
              /*if(full_array.length==0){                         
                full_array[activity.id] = finalActualEndDate;
              }
              else if(full_array.length>0){
                alert(full_array[activity.id])
                full_array[activity.id] = finalActualEndDate;      
              } */
              if(full_array.length==0){
                full_array[activity.id] = finalActualEndDate;
                activity_id = activity.id;
              }
              else if(finalActualEndDate>full_array[activity_id]){
                full_array.length='';
                full_array[activity.id] = finalActualEndDate;
                activity_id = activity.id;     
              }

            }
            //alert(full_array[activity_id])

            $.ajax({
              type: 'POST',
              url: '../projectsmain/getcriticalpath',
              dataType: "json",
              async:false,
              data: {activityid: activity_id , enddate: full_array[activity_id]},
              success: function(data){
                //alert(data.criticalIDs)
                //criticalid.push(data.criticalIDs);
                criticalid = data.criticalIDs;
               // console.log(criticalid);
              }
            });
        }
    });
    
    $.ajax({
          type: 'POST',
          url: '../projectsmain/getitems',
          dataType: "json",
          async:false,
          data: {projectid:<?= $projectId ?>},
          success: function(data){
            // var items=JSON.parse(data.result);
              // console.log(data.result);
              // alert(data.error);
              for (i = 0; i < data.result.length; i++) {
                var item = data.result[i];
                console.log(item);
                g.AddTaskItem(new JSGantt.TaskItem(item.scheduleitem_id,   item.name,     '',           '',          'ggroupblack',  '', 0, 'Brian',    100,     1,      0,       1,     '',      '',      'Some Notes text', g ));
                $.ajax({
                    type: 'POST',
                    url: '../projectsmain/getitemactivites',
                    dataType: "json",
                    async:false,
                    data: {projectid:<?= $projectId ?>, itemId: item.scheduleitem_id},
                    success: function(data1){
                      // var items=JSON.parse(data.result);
                        // console.log(data1.result);
                        // alert(data.error);
                        for (j = 0; j < data1.result.length; j++) {
                          var activity = data1.result[j];
                          var random = 'ABC'+activity.id;
                         
                          var startDate = Date.parse(activity.start_date);
                          var endDate = Date.parse(activity.end_date);
                          var timeDiff = endDate - startDate;
                          daysDiff = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
                          budgetedDayDifference = daysDiff+1;

                          var actualStartDate = Date.parse(activity.actual_start_date);
                          console.log('real'+activity.actual_end_date);
                          if(activity.actual_end_date != null)
                          {
                            var actualEndDate = Date.parse(activity.actual_end_date);

                          }
                          else
                          {
                            // alert()
                            var myDate = new Date(activity.actual_start_date);
                            myDate.setDate(myDate.getDate()+(activity.duration-1));
                            var day = myDate.getDate();
                            var month = myDate.getMonth() + 1;
                            var year = myDate.getFullYear();
                            if (day < 10) {
                                day = "0" + day;
                            }
                            if (month < 10) {
                                month = "0" + month;
                            // var date = day + "/" + month + "/" + year;
                            }
                            var actualEndDateString = year+'-'+month+'-'+day;
                            var actualEndDate =  Date.parse(actualEndDateString); 
                            // alert(actualEndDate);
                          }
                          var actualtimeDiff = actualEndDate - actualStartDate;
                          actualdaysDiff = Math.floor(actualtimeDiff / (1000 * 60 * 60 * 24));
                          actualDayDifference = actualdaysDiff+1;
                          // alert(actualDayDifference);

                          var totalQuantity = activity.quantity;
                          var completedQuantity = activity.completedQuantity;
                          // alert(completedQuantity);
                          if(totalQuantity == completedQuantity || completedQuantity == null || completedQuantity == 0 )
                          {
                            // var percentageDifference = (actualDayDifference/budgetedDayDifference)*100;
                            // console.log(completedQuantity);
                            if(completedQuantity ==null)
                              finalActEndDate = actualEndDateString;
                            else
                              finalActEndDate = activity.actual_end_date;

                            // alert(finalActEndDate);
                          }
                          else
                          {
                            var dayTakenFOrOneUnitQuantity = actualDayDifference/completedQuantity;
                            var totalDaysForCompletion = dayTakenFOrOneUnitQuantity*totalQuantity;
                            // var finalActEndDate ;
                            var myDate = new Date(activity.actual_start_date);
                            myDate.setDate(myDate.getDate()+(totalDaysForCompletion-1));
                            console.log(myDate);
                            // formating date
                            var day = myDate.getDate();
                            var month = myDate.getMonth() + 1;
                            var year = myDate.getFullYear();
                            if (day < 10) {
                                day = "0" + day;
                            }
                            if (month < 10) {
                                month = "0" + month;
                            }
                            // var date = day + "/" + month + "/" + year;

                            finalActEndDate = year+'-'+month+'-'+day;
                          
                            // var percentageDifference = (totalDaysForCompletion/budgetedDayDifference)*100;
                          }
                          console.log("activity.actual_start_date= "+activity.actual_start_date);
                          console.log("activity.actual_end_date= "+activity.actual_end_date);
                          console.log("completedQuantity= "+completedQuantity);
                          console.log("finalActEndDate= "+finalActEndDate);
                          // alert(finalActEndDate);
                          endDateString = new Date(activity.end_date);
                          actualEndDateString = new Date(finalActEndDate);

                          if($.inArray(activity.id, criticalid) !== -1){
                            //$class = 'gtaskgreen';
                            //$class = 'gtaskblue';
                            var float = Number(activity.float);
                            var delay = Number(activity.delay);
                            /*if(float > delay){
                              $class = 'gtaskpink';
                            }
                            else{*/
                              if(activity.activity_start<activity.actual_start_date){
                                $class = 'gtaskpurple';
                                if(activity.quantity==activity.reprtdqty){
                                  $class = 'gtaskyellow';
                                }
                              }
                              else{
                                $class = 'gtaskpink';
                                if(activity.quantity==activity.reprtdqty){
                                  $class = 'gtaskyellow';
                                }
                              }
                            //}
                          }
                          else{
                            /*if(actualEndDateString > endDateString){
                              $class = 'gtaskred';
                            }
                            else{
                              if(activity.float>0){
                                var float = Number(activity.float);
                                var delay = Number(activity.delay);
                                if(float > delay){
                                  $class = 'gtaskgreen';
                                }
                                else{
                                  $class = 'gtaskblue';
                                }
                              }
                              else{*/
                                if(activity.activity_start<activity.actual_start_date){
                                  $class = 'gtaskblue';
                                  if(activity.quantity==activity.reprtdqty){
                                    $class = 'gtaskyellow';
                                  }
                                }
                                else{
                                  $class = 'gtaskgreen';
                                  if(activity.quantity==activity.reprtdqty){
                                    $class = 'gtaskyellow';
                                  }
                                }

                             // }
                            //}
                          }
                          
                          var today = new Date();
                          var dd = today.getDate();
                          var mm = today.getMonth() + 1;

                          var yyyy = today.getFullYear();
                          if (dd < 10) {
                            dd = '0' + dd;
                          } 
                          if (mm < 10) {
                            mm = '0' + mm;
                          } 
                          var today = yyyy + '/' + mm + '/' + dd;

                          var budgeted_start_date = activity.start_date;
                          if(budgeted_start_date==null){ BudgetedStartDate = today; }
                          else if(budgeted_start_date=='0000-00-00'){ BudgetedStartDate = today; }
                          else{ BudgetedStartDate = activity.start_date; }                         
                          
                          var budgeted_end_date = activity.end_date;
                          if(budgeted_end_date==null){ BudgetedEndDate = today; }
                          else if(budgeted_end_date=='0000-00-00'){ BudgetedEndDate = today; }
                          else{ BudgetedEndDate = activity.end_date; } 

                          var actual_start_date = activity.actual_start_date;                         
                          if(actual_start_date==null){ finalActualStartDate = today; }
                          else if(actual_start_date=='0000-00-00'){ finalActualStartDate = today; }
                          else{ finalActualStartDate = activity.actual_start_date; }
                          
                          if(finalActEndDate==null){ finalActualEndDate = today; }
                          else if(finalActEndDate=='0000-00-00'){ finalActualEndDate = today; }
                          else{ finalActualEndDate = finalActEndDate;}
                          
                          var percentagecompleted = (completedQuantity/totalQuantity)*100;
                          //var finalpercentagecompleted = (percentagecompleted).toFixed(2);
                          /*if(activity.float>0){
                            var finalpercentagecompleted = activity.float;
                          }
                          else{*/
                            var finalpercentagecompleted = activity.delaystart;
                          //}
                          
                          //var relation1 = activity.precedent_activity+'ABC'+activity.relation;
                          var relation1 = activity.relation;
                          //alert(relation1)
                          //g.AddTaskItem(new JSGantt.TaskItem(activity.id+random, activity.name, BudgetedStartDate, BudgetedEndDate, $class,    '', 0, 'Brian T.', null,    0,      activity.scheduleitem_id,      1,     '',      '',      '', g,0, finalActualStartDate,finalActualEndDate));
                          //g.AddTaskItem(new JSGantt.TaskItem(activity.id+random, activity.name, finalActualStartDate, finalActualEndDate, $class,    '', 0, 'Brian T.', finalpercentagecompleted,    0,      activity.scheduleitem_id,      1,     relation1,      '',      '', g,0, BudgetedStartDate,BudgetedEndDate));

                          if(activity.activity_start<=finalActualStartDate){
                            var planstartdate = activity.activity_start;
                          }
                          else{
                            var planstartdate = finalActualStartDate;
                          }
                          //var planenddate = activity.activity_end;

                          if(activity.duration>activity.olddur){
                            var oldur = activity.olddur - 1;
                            var today = new Date(finalActualStartDate);
                            //today.setHours(23);
                            var date = today.setDate(today.getDate()+oldur);
                            var planenddate1 = new Date(date);
                            var dd = planenddate1.getDate();
                            var mm = planenddate1.getMonth() + 1;

                            var yyyy = planenddate1.getFullYear();
                            if (dd < 10) {
                              dd = '0' + dd;
                            } 
                            if (mm < 10) {
                              mm = '0' + mm;
                            } 
                            var planenddate = yyyy + '-' + mm + '-' + dd;
                            //var planenddate = '2020-06-15';
                          }
                          else{
                            var planenddate = finalActualEndDate;
                          }

                          var actualstartdate = finalActualStartDate;
                          var actualenddate = finalActualEndDate;

                          g.AddTaskItem(new JSGantt.TaskItem(activity.id+random, activity.name, planstartdate, actualenddate, $class,    '', 0, activity.bduration, finalpercentagecompleted,    0,      activity.scheduleitem_id,      1,     relation1,      '',      '', g, activity.quantity,actualstartdate,planenddate));

                          //g.AddTaskItem(new JSGantt.TaskItem(activity.id+random, activity.name, finalActualStartDate, finalActualEndDate, $class,    '', 0, activity.bduration, finalpercentagecompleted,    0,      activity.scheduleitem_id,      1,     relation1,      '',      '', g, activity.quantity));
                          //g.AddTaskItem(new JSGantt.TaskItem(activity.id+random, activity.name, '2020-04-01', '2020-05-24', $class,    '', 0, activity.bduration, '10%',    0,      activity.scheduleitem_id,      1,     relation1,      '',      '', g, activity.quantity,'2020-05-01', '2020-05-24'));
                          //g.AddTaskItem(new JSGantt.TaskItem(activity.id+random, activity.name, activity.actual_start_date, activity.actual_end_date, finalActualStartDate, finalActualEndDate, $class,    '', 0, activity.bduration, finalpercentagecompleted,    0,      activity.scheduleitem_id,      1,     relation1,      '',      '', g, activity.quantity));

                         // var randomnumber = Date.now() + Math.random();
                        //  g.AddTaskItem(new JSGantt.TaskItem(activity.id+randomnumber, '',     activity.actual_start_date,activity.actual_end_date, 'gtaskred',    '', 0, 'Brian T.', null,    0,      activity.scheduleitem_id,      1,     '',      '',      '',      g));
                          
                        }
                    }
                });

              }
          }
      });
    
    // Parameters                     (pID, pName,                  pStart,       pEnd,        pStyle,         pLink (unused)  pMile, pRes,       pComp, pGroup, pParent, pOpen, pDepend, pCaption, pNotes, pGantt)
    // g.AddTaskItem(new JSGantt.TaskItem(1,   'Define Chart API',     '',           '',          'ggroupblack',  '',                 0, 'Brian',    0,     1,      0,       1,     '',      '',      'Some Notes text', g ));
    // g.AddTaskItem(new JSGantt.TaskItem(11,  'Chart Object',         '2017-02-20','2017-02-20', 'gmilestone',   '',                 1, 'Shlomy',   100,   0,      1,       1,     '',      '',      '',      g));
    // g.AddTaskItem(new JSGantt.TaskItem(12,  'Task Objects',         '',           '',          'ggroupblack',  '',                 0, 'Shlomy',   40,    1,      1,       1,     '',      '',      '',      g));
    // g.AddTaskItem(new JSGantt.TaskItem(121, 'Constructor Proc',     '2017-02-21','2017-03-09', 'gtaskblue',    '',                 0, 'Brian T.', 60,    0,      12,      1,     '',      '',      '',      g));
    // g.AddTaskItem(new JSGantt.TaskItem(122, 'Task Variables',       '2017-03-06','2017-03-11', 'gtaskred',     '',                 0, 'Brian',    60,    0,      12,      1,     121,     '',      '',      g));
    // g.AddTaskItem(new JSGantt.TaskItem(123, 'Task by Minute/Hour',  '2017-03-09','2017-03-14 12:00', 'gtaskyellow', '',            0, 'Ilan',     60,    0,      12,      1,     '',      '',      '',      g));
    // g.AddTaskItem(new JSGantt.TaskItem(124, 'Task Functions',       '2017-03-09','2017-03-29', 'gtaskred',     '',                 0, 'Anyone',   60,    0,      12,      1,     '123SS', 'This is a caption', null, g));
    // g.AddTaskItem(new JSGantt.TaskItem(2,   'Create HTML Shell',    '2017-03-24','2017-03-24', 'gtaskyellow',  '',                 0, 'Brian',    20,    0,      0,       1,     122,     '',      '',      g));
    // g.AddTaskItem(new JSGantt.TaskItem(3,   'Code Javascript',      '',           '',          'ggroupblack',  '',                 0, 'Brian',    0,     1,      0,       1,     '',      '',      '',      g));
    // g.AddTaskItem(new JSGantt.TaskItem(31,  'Define Variables',     '2017-02-25','2017-03-17', 'gtaskpurple',  '',                 0, 'Brian',    30,    0,      3,       1,     '',      'Caption 1','',   g));
    // g.AddTaskItem(new JSGantt.TaskItem(32,  'Calculate Chart Size', '2017-03-15','2017-03-24', 'gtaskgreen',   '',                 0, 'Shlomy',   40,    0,      3,       1,     '',      '',      '',      g));
    // g.AddTaskItem(new JSGantt.TaskItem(33,  'Draw Task Items',      '',           '',          'ggroupblack',  '',                 0, 'Someone',  40,    2,      3,       1,     '',      '',      '',      g));
    // g.AddTaskItem(new JSGantt.TaskItem(332, 'Task Label Table',     '2017-03-06','2017-03-09', 'gtaskblue',    '',                 0, 'Brian',    60,    0,      33,      1,     '',      '',      '',      g));
    // g.AddTaskItem(new JSGantt.TaskItem(333, 'Task Scrolling Grid',  '2017-03-11','2017-03-20', 'gtaskblue',    '',                 0, 'Brian',    0,     0,      33,      1,     '332',   '',      '',      g));
    // g.AddTaskItem(new JSGantt.TaskItem(34,  'Draw Task Bars',       '',           '',          'ggroupblack',  '',                 0, 'Anybody',  60,    1,      3,       0,     '',      '',      '',      g));
    // g.AddTaskItem(new JSGantt.TaskItem(341, 'Loop each Task',       '2017-03-26','2017-04-11', 'gtaskred',     '',                 0, 'Brian',    60,    0,      34,      1,     '',      '',      '',      g));
    // g.AddTaskItem(new JSGantt.TaskItem(342, 'Calculate Start/Stop', '2017-04-12','2017-05-18', 'gtaskpink',    '',                 0, 'Brian',    60,    0,      34,      1,     '',      '',      '',      g));
    // g.AddTaskItem(new JSGantt.TaskItem(343, 'Draw Task Div',        '2017-05-13','2017-05-17', 'gtaskred',     '',                 0, 'Brian',    60,    0,      34,      1,     '',      '',      '',      g));
    // g.AddTaskItem(new JSGantt.TaskItem(344, 'Draw Completion Div',  '2017-05-17','2017-06-04', 'gtaskred',     '',                 0, 'Brian',    60,    0,      34,      1,     "342,343",'',     '',      g));
    // g.AddTaskItem(new JSGantt.TaskItem(35,  'Make Updates',         '2017-07-17','2017-09-04', 'gtaskpurple',  '',                 0, 'Brian',    30,    0,      3,       1,     '333',   '',      '',      g));

    g.Draw();
  } else {
    alert("Error, unable to create Gantt Chart");
  }
 correctWidth();
}
function correctWidth()
{
    var width = $( "#embedded-Ganttglistbody" ).find( ".gtaskname" ).width();
    //alert(width);
    $( "#embedded-Ganttglisthead" ).find( ".gtaskname" ).css( "width", width+3);
}
$(document).ready(function() {
  
  loadGantt();
   $('.save-relation').click(function(){
    var relation = $(this).val();
    var firstItem = $('#precedent_schedule_item_'+relation).val();
    var firstActivity = $('#precedent_activity_'+relation).val();
    var secondItem = $('#dependent_schedule_item_'+relation).val();
    var secondActivity = $('#dependent_activity_'+relation).val();
    var relationType = $('#relation_type_'+relation).val();
    var lag = $('#activitylag_'+relation).val();
    $.ajax({
            type: 'POST',
            url: '../projectsmain/updatestructurerelation',
            beforeSend : function(){
                $('#save-relation_'+relation).attr("disabled", true);
                $('#save-relation_'+relation).css( "background-color", '#d4c4c2');
            },
            dataType: "json",
            data: {id:relation,firstItem: firstItem, firstActivity: firstActivity, secondItem: secondItem, secondActivity: secondActivity, relationType: relationType, projectId: $('#selectedProjectId').val(),lag:lag},
            success: function(data){
                if(data.error=='No')
                {
                    loadGantt();
                    // $( ".edit_toggle" ).trigger('click');
                    $('#ganttchartitems').html(data.datarows);
                    // $('#structure_relation_list').html(data.relationList);
                    // $('#wbsscheduleitems').hide();
                    // $('#wbsstructureitems').show();
                    // $('#activity_relation_list').hide();
                    // $('#structure_relation_list').show();
                    // $('#oldstructure').attr("disabled", true);
                }
                else
                {
                    //alert(data.errortext);
                }

                $('#save-relation_'+relation).attr("disabled", false);
                $('#save-relation_'+relation).css( "background-color", '#99281f');
            }
        });
    
  });
  $('#submit_form').click(function(){
    // alert('success');
    $.ajax({
        type: 'POST',
        url: '../projectsmain/savescheduledata',
        dataType: "json",
        async:false,
        data: $('#activities_edit_form').serialize(),
        success: function(data){
          if(data.error == 'No')
          {
            // alert('true');
            loadGantt();
            $( "#close-edit" ).trigger('click');
          }
          else{
            // console.log('error');
            alert('Please correct the dates');
          }      
        }
    });
    
  });

  $( ".edit_toggle" ).click(function() {
    if($("#B").is(":visible"))
      $('#B').hide();
    else
      $('#B').delay(1000).show(1);
      $( "#A" ).toggle( "slow", function() {
      // Animation complete.
    });
    //loadGantt();
  });
  
   $( ".edit_relation" ).click(function() { 
    if($("#B").is(":visible"))
      $('#B').hide();
    else
      $('#B').delay(1000).show(1);
    $( "#C" ).toggle( "slow", function() {
      
      // Animation complete.
    });
  });
  $("#close").click(function(){
    $('#C').hide();
    $( "#B" ).toggle( "slow", function() {
      
      // Animation complete.
    });
    loadGantt();
  });
  $("#close-edit").click(function(){
    $('#A').hide();
    $( "#B" ).toggle( "slow", function() {
      
      // Animation complete.
    });
    loadGantt();
  });  

  $('.date_field_start').change(function(){
      // alert(this.value);
      var dataId = $(this).attr('data-id');
      var activityId = dataId.substr(dataId.lastIndexOf("_") + 1);
      // alert(activityId);
      var startDate = new Date($('.start_date_'+activityId).val());
      var endDate = $('.end_date_'+activityId).val();
      var duration = $('.duration_'+activityId).val();
      // console.log(endDate);
        if(duration != '' && startDate != '')
        {
          var newdate = new Date(startDate).setDate(startDate.getDate() + (+duration) - 1);
          var endDate1 = new Date(newdate);
          var tempoMonth = (endDate1.getMonth() + 1);
          if (tempoMonth < 10) tempoMonth = '0' + tempoMonth;
          var tempoDate = (endDate1.getDate());
          if (tempoDate < 10) tempoDate = '0' + tempoDate;
          var endDate = endDate1.getFullYear() + '-' + tempoMonth + '-' + tempoDate;

          $('.end_date_'+activityId).val(endDate);
        }
    
      $.ajax({
        type: 'POST',
        url: '../projectsmain/checkdatevalidity',
        dataType: "json",
        async:false,
        data: {date: this.value, dataId:dataId },
        success: function(data){
          if(data.error == 'No')
          {
            // console.log(data.result);
            for (i = 0; i < data.result.length; i++) {
              var warning = data.result[i];
              console.log($('.'+warning.id).val());
              if($('.'+warning.id).val() != warning.date)
              {
                var endDate1 = new Date(warning.date);
                var tempoMonth = (endDate1.getMonth() + 1);
                if (tempoMonth < 10) tempoMonth = '0' + tempoMonth;
                var tempoDate = (endDate1.getDate());
                if (tempoDate < 10) tempoDate = '0' + tempoDate;
                var endDate = tempoDate + '-' + tempoMonth + '-' + endDate1.getFullYear();
                $('#'+warning.id).html('This date should change into '+endDate);
                $('#'+warning.id).show();
                $('.'+warning.id).css('border','1px solid red');
              }
              else
              {
                $('#'+warning.id).hide();
                $('.'+warning.id).css('border','');
              }
            // $( ".edit_toggle" ).trigger('click');
            }
          }
          else{
            console.log('error');
          }      
        }
    })
              //Date in full format alert(new Date(this.value));
      // var inputDate = new Date(this.value);
  });
});
  
$(document).on('click','.delete-relation',function(){
    var relationid=$(this).val();
    var r = confirm("Are you sure you want to delete this relation ?");
     if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../projectsmain/deleterelation',
            beforeSend : function(){
                $('#delete-relation_'+relationid).attr("disabled", true);
            },
            dataType: "json",
            data: {relationId:relationid },
            success: function(data){
                if(data.error=='No')
                {
                    $('#relationrow_'+relationid).hide();
                }
                else
                {
                    alert(data.errortext);
                }
            }
        });
   }
});
  
  $('.date_field_end').change(function(){
      // alert(this.value);
      var dataId = $(this).attr('data-id');
      var activityId = dataId.substr(dataId.lastIndexOf("_") + 1);
      // alert(activityId);
      var startDate = new Date($('.start_date_'+activityId).val());
      var endDate = $('.end_date_'+activityId).val();
      var duration = $('.duration_'+activityId).val();
   
        var startDate = $('.start_date_'+activityId).val();
        var endDate = $('.end_date_'+activityId).val();
        // console.log(endDate);
        if(endDate != '' && startDate != '')
        {
            var startDate1 = Date.parse(startDate);
            var endDate1 = Date.parse(endDate);
            var timeDiff = endDate1 - startDate1;
            daysDiff = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
            console.log(daysDiff);
            $('.duration_'+activityId).val(daysDiff+1);
        }
       
        // console.log('null');
      // return;
      $.ajax({
        type: 'POST',
        url: '../projectsmain/checkdatevalidity',
        dataType: "json",
        async:false,
        data: {date: this.value, dataId:dataId },
        success: function(data){
          if(data.error == 'No')
          {
            // console.log(data.result);
            for (i = 0; i < data.result.length; i++) {
              var warning = data.result[i];
              console.log($('.'+warning.id).val());
              if($('.'+warning.id).val() != warning.date)
              {
                var endDate1 = new Date(warning.date);
                var tempoMonth = (endDate1.getMonth() + 1);
                if (tempoMonth < 10) tempoMonth = '0' + tempoMonth;
                var tempoDate = (endDate1.getDate());
                if (tempoDate < 10) tempoDate = '0' + tempoDate;
                var endDate = tempoDate + '-' + tempoMonth + '-' + endDate1.getFullYear();
                $('#'+warning.id).html('This date should change into '+endDate);
                $('#'+warning.id).show();
                $('.'+warning.id).css('border','1px solid red');
              }
              else
              {
                $('#'+warning.id).hide();
                $('.'+warning.id).css('border','');
              }
            // $( ".edit_toggle" ).trigger('click');
            }
          }
          else{
            console.log('error');
          }      
        }
    })
              //Date in full format alert(new Date(this.value));
      // var inputDate = new Date(this.value);
  });

 $('.duration').change(function(){
    var dataId = $(this).attr('data-id');
    var activityId = dataId.substr(dataId.lastIndexOf("_") + 1);
    var today = new Date($('.start_date_'+activityId).val());
    var duration =  $('.duration_'+activityId).val();

    if(duration != '' && startDate != '')
    {
        var newdate = new Date(today).setDate(today.getDate() + (+duration) - 1);
        var endDate1 = new Date(newdate);
        var tempoMonth = (endDate1.getMonth() + 1);
        if (tempoMonth < 10) tempoMonth = '0' + tempoMonth;
        var tempoDate = (endDate1.getDate());
        if (tempoDate < 10) tempoDate = '0' + tempoDate;
        var endDate = endDate1.getFullYear() + '-' + tempoMonth + '-' + tempoDate;
      
        var tempoStart = (today.getDate());
        if (tempoStart < 10) tempoStart = '0' + tempoStart;
        var tempoMonth = (today.getMonth() + 1);
        if (tempoMonth < 10) tempoMonth = '0' + tempoMonth;
        var startDate = today.getFullYear() + '-' + tempoMonth + '-' + tempoStart;

        $('.start_date_'+activityId).val(startDate);
        $('.end_date_'+activityId).val(endDate);
    }
    $.ajax({
        type: 'POST',
        url: '../projectsmain/checkdatevalidity',
        dataType: "json",
        async:false,
        data: {date: $('.end_date_'+activityId).val(), dataId:dataId },
        success: function(data){
          if(data.error == 'No')
          {
            // console.log(data.result);
            for (i = 0; i < data.result.length; i++) {
              var warning = data.result[i];
              console.log($('.'+warning.id).val());
              if($('.'+warning.id).val() != warning.date)
              {
                var endDate1 = new Date(warning.date);
                var tempoMonth = (endDate1.getMonth() + 1);
                if (tempoMonth < 10) tempoMonth = '0' + tempoMonth;
                var tempoDate = (endDate1.getDate());
                if (tempoDate < 10) tempoDate = '0' + tempoDate;
                var endDate = tempoDate + '-' + tempoMonth + '-' + endDate1.getFullYear();
                $('#'+warning.id).html('This date should change into '+endDate);
                $('#'+warning.id).show();
                $('.'+warning.id).css('border','1px solid red');
              }
              else
              {
                $('#'+warning.id).hide();
                $('.'+warning.id).css('border','');
              }
            // $( ".edit_toggle" ).trigger('click');
            }
          }
          else{
            console.log('error');
          }      
        }
    })
    
}); 
  
$(document).on('change','.precedent_schedule_item',function(){
     var scheduleItem= $(this).val();
    var dataid= $(this).attr("data-id");
   // alert(scheduleItem)
    // if($('#schedule_item_second').val()=='' )
    //     $('#schedule_item_second').val(scheduleItem);
    $.ajax({
        type: 'POST',
        url: '../projectsmain/getscheduleactivity',
        dataType: "json",
        data: {scheduleItem: scheduleItem},
        success: function(data){
            if(data.error=='No')
            {
                $('#precedent_activity_'+dataid).html(data.result);
            }
        }
    });
});  
  
$(document).on('change','.dependent_schedule_item',function(){
     var scheduleItem= $(this).val();
    var dataid= $(this).attr("data-id");
   // alert(scheduleItem)
    // if($('#schedule_item_second').val()=='' )
    //     $('#schedule_item_second').val(scheduleItem);
    $.ajax({
        type: 'POST',
        url: '../projectsmain/getscheduleactivity',
        dataType: "json",
        data: {scheduleItem: scheduleItem},
        success: function(data){
            if(data.error=='No')
            {
                $('#dependent_activity_'+dataid).html(data.result);
            }
        }
    });
});  
  
$(document).on('change','.relation_type',function(){
  var relationtype = $(this).val();
  var dataid= $(this).attr("data-id");
  //if(relationtype==1 || relationtype==3){
    $('#activitylag_'+dataid).show();
  /*}
  else{
    $('#activitylag_'+dataid).val('');
    $('#activitylag_'+dataid).hide();
  }*/
});

/*$(document).ready(function () {  
      $(".gtaskpurple").click(function(){         
           $('#myModal').modal('show');
      });
  });*/

  /*$(document).ready(function () {  
      $(".gitemweek").click(function(){      
          var string = $(this).find(".gtaskcell").attr('class');
          var myStringArray = string.split(' ');
          var replace0 = myStringArray[1].replace("activityid", "");
          var replace1 = replace0.replace("ABC", " ");
          var activityid = replace1.split(' ');
          //alert(activityid[0])

          google.charts.load("current", {packages:['corechart']});
          google.charts.setOnLoadCallback(drawChart);
          function drawChart() {
              var data = google.visualization.arrayToDataTable([
                ["Element", "Density", { role: "style" } ],
                ["Copper", 8.94, "#b87333"],
                ["Silver", 10.49, "silver"],
                ["Gold", 19.30, "gold"],
                ["Platinum", 21.45, "color: #e5e4e2"]
              ]);

              var view = new google.visualization.DataView(data);
              view.setColumns([0, 1,
                              { calc: "stringify",
                                sourceColumn: 1,
                                type: "string",
                                role: "annotation" },
                              2]);

              var options = {
                title: "Density of Precious Metals, in g/cm^3",
                width: 600,
                height: 400,
                bar: {groupWidth: "95%"},
                legend: { position: "none" },
              };
              var chart = new google.visualization.ColumnChart(document.getElementById("columnchart_values"));
              chart.draw(view, options);
          }

          $('#myModal').modal('show');
      });
  });*/
  
$( "#ganttchartitems" ).sortable({
        items: '.no',
        update:function( event, ui ) {
            //alert($(this).index());
            var updatedrows=[];
            $(this).closest('table').find('tbody tr.ui-state-default').each(function (i) {
                var rowid=$(this).attr('data-id');
                var rowindex=$(this).index();
                updatedrows.push({
                    rowid: rowid,
                    rowindex:rowindex
                })
            });
            $.ajax({
                type: 'POST',
                url: '../updateganttsort',
                data: {datavalue:updatedrows},
                dataType: "json",
                success: function(data){}
            });
        }

    }).disableSelection()
          </script>

</div></div></div>

</body>
</html>