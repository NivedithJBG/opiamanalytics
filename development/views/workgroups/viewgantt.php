<script src="<?php echo Yii::app()->request->baseUrl; ?>/codebase/dhtmlxgantt.js"></script>
<link href="<?php echo Yii::app()->request->baseUrl; ?>/codebase/dhtmlxgantt.css" rel="stylesheet">
<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/jquery-1.9.1.min.js"></script>
<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/jquery-ui.css">
<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/jquery-ui.js"></script>
<link href="<?php echo Yii::app()->request->baseUrl; ?>/css/menu.css" rel="stylesheet">
<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/style.css">
<link href="<?php echo Yii::app()->request->baseUrl; ?>/css/jumbotron-narrow.css" rel="stylesheet">
<script type="text/javascript">

    $(function(){
        unavailableDates =[ <?php echo $calander;?> ];// checking for holidays
        $('#calander').click(function ()
        {
            $('#selecteddate').toggle();

        });
        function dateselected(date,inst)
        {
            $.ajax({
                type: 'POST',
                url: '<?php echo Yii::app()->request->baseUrl; ?>/projects/checkholiday',
                dataType: "json",
                data: {date:date,Project_Id:'<?php echo $_GET['id'];?>'},
                success: function(data){
                    if(data.action=='remove')
                    {
                        var arrayindex = unavailableDates.indexOf(data.date);
                        if (arrayindex > -1) {
                            unavailableDates.splice(arrayindex, 1);
                        }
                        $( "#selecteddate" ).datepicker( "refresh" );
                    }
                    else
                    {
                        unavailableDates.push(data.date);
                        $( "#selecteddate" ).datepicker( "refresh" );


                    }
                }
            });

        }
        function unavailable(date) {

            dmy = date.getDate() + "-" + (date.getMonth()+1) + "-" + date.getFullYear();

            if ($.inArray(dmy, unavailableDates) < 0) {
                if(date.getDay()==0)
                {
                    return [false,"holidayclass","Weekend"];
                }
                else
                {
                    return [true,"workingdayclass",""];
                }

            } else {
                return [true,"holidayclass","Holiday"];
            }
        }
        $('#selecteddate').datepicker({ showButtonPanel: true,beforeShowDay: unavailable,onSelect:dateselected})
        });
    $(document).on('click','.ui-datepicker-customclose',function(){
        $('#selecteddate').hide();
        location.reload();
    });
</script>
<title>Gant view</title>
<a href="<?php echo Yii::app()->createUrl('site/index')?>" ><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/logo.png" id="logoimage"/></a>
<div class="header" style="border:0px">
    <nav class="clearfix">
        <ul class="clearfix">
            <?php if(Yii::app()->user->isGuest): ?>
                <li><a href="<?php echo Yii::app()->createUrl('user/user/login')?>">Login</a></li>
            <?php else:?>
                <?php if(User::model()->isAdmin()): ?>
                    <li><a href="<?php echo Yii::app()->createUrl('user/user/admin')?>">Users</a></li>
                    <li><a href="<?php echo Yii::app()->createUrl('projects1/index')?>">Projects</a></li>
                    <li><a href="<?php echo Yii::app()->createUrl('projects/masters')?>">Masters</a></li>
                <?php endif;?>
                <li><a href="<?php echo Yii::app()->createUrl('FinanceRequests/index')?>">Finance</a></li>
                <li><a href="<?php echo Yii::app()->createUrl('projects/report')?>">Report</a></li>
                <li><a href="<?php echo Yii::app()->createUrl('user/user/logout')?>">Logout</a></li>
            <?php endif;?>
        </ul>
        <a href="#" id="pull">Menu</a>
    </nav>
</div>
<div style="width:330px;height: 30px;margin-top: 10px;font-family: arial;font-size: 13px;">
    <input type="radio" id="scale1" name="scale" value="1" /><label for="scale1">Days</label>
    <input type="radio" id="scale2" name="scale" value="2"  checked/><label for="scale2">Weeks</label>
    <input type="radio" id="scale3" name="scale" value="3" /><label for="scale3">Months</label>
    <input type="radio" id="scale4" name="scale" value="4" /><label for="scale4">Years</label>
    <div id="calander">Calendar</div>
    <div id="selecteddate"></div>

</div>
<div id="gantt_here" style='width:100%; height:80%;'></div>
<div style="width:100%;height: 30px;margin-top: 10px;">
    <!--<div style="padding:5px;background-color:#000099;color: white;font-family: arial;font-size: 13px;float: left;margin: 5px;">Project</div>-->
    <div style="padding:5px;background-color:#afafaf;color: white;font-family: arial;font-size: 13px;float: left;margin: 5px;">Workgroup</div>
    <div style="padding:5px;background-color:#8EDFF0;color: white;font-family: arial;font-size: 13px;float: left;margin: 5px;">Activity Budgeted</div>
    <div style="padding:5px;background-color:#0066cc;color: white;font-family: arial;font-size: 13px;float: left;margin: 5px;">Activity Actual</div>
    <div style="padding:5px;background-color:#eff5fd;color: white;font-family: arial;font-size: 13px;float: left;margin: 5px;color: #afafaf">Holidays</div>
    <!--<a href="<?php /*echo Yii::app()->request->baseUrl; */?>/projects/deletelinks/<?php /*echo $model->Project_Id;*/?> " onclick="return confirm('Are you sure want to delete all links ? This will delete all the links and reset dates into project starting date');" ><button type="button" id="deletelinks" style="padding:5px;border:0px;background-color:rgba(27,200,242,0.77);color: white;font-family: arial;font-size: 13px;float: left;margin: 5px;">Delete links</button></a>-->
</div>
<script type="text/javascript">
    function setScaleConfig(value){
        switch (value) {
            case "1":
                gantt.config.work_time = true;
                gantt.config.scale_unit = "day";
                gantt.config.duration_unit = "day";

                gantt.config.date_scale = "%d %M";
                gantt.config.subscales = [{unit:"month", step:1, date:"%F, %Y"}];
                gantt.config.scale_height = 27;
                //gantt.showDate(new Date(<?php echo $gantstartdate[0];?>, <?php echo $gantstartdate[1];?>, <?php echo $gantstartdate[2];?>));
                gantt.setWorkTime({day : 6});//Saturdays are also work days
                break;
            case "2":
                var weekScaleTemplate = function(date){
                    var dateToStr = gantt.date.date_to_str("%d %M");
                    var endDate = gantt.date.add(gantt.date.add(date, 1, "week"), -1, "day");
                    return dateToStr(date) + " - " + dateToStr(endDate);
                };

                gantt.config.scale_unit = "week";
                gantt.config.step = 1;
                gantt.templates.date_scale = weekScaleTemplate;
                /*                gantt.config.subscales = [
                 {unit:"day", step:1, date:"%d" }
                 ]*/
                gantt.config.scale_height = 50;
                break;
            case "3":
                gantt.config.scale_unit = "month";
                gantt.config.date_scale = "%F, %Y";
                /*                gantt.config.subscales = [
                 {unit:"day", step:1, date:"%j, %D" }
                 ];*/
                gantt.config.scale_height = 50;
                gantt.templates.date_scale = null;
                break;
            case "4":
                gantt.config.scale_unit = "year";
                gantt.config.step = 1;
                gantt.config.date_scale = "%Y";
                gantt.config.min_column_width = 50;

                gantt.config.scale_height = 90;
                gantt.templates.date_scale = null;

                break;
        }
    }

    var holidays = [

        <?php echo $offdays;?>

    ];
    for(var i=0; i < holidays.length; i++){
        gantt.setWorkTime({
            date:holidays[i],
            hours:false
        });
    }
    var dateToStr = gantt.date.date_to_str("%d %F");
    dhtmlx.message("Following holidays are excluded from working time:");
    for(var i =0; i < holidays.length; i++){
        setTimeout(
            (function(i){
                return function(){
                    dhtmlx.message(dateToStr(holidays[i]))
                } })(i)
            ,
            (i+1)*600
        );
    }
    setScaleConfig('2');
    //gantt.config.start_date = new Date(<?php echo $gantstartdate[0];?>, <?php echo $gantstartdate[1]-1;?>, <?php echo $gantstartdate[2];?>);
    //gantt.config.end_date = new Date(<?php echo $gantenddate[0];?>, <?php echo $gantenddate[1]-1;?>, <?php echo $gantenddate[2];?>);
    gantt.config.xml_date = "%d-%m-%Y";
    gantt.config.drag_resize = false;
    gantt.config.drag_progress = false;
    gantt.config.show_progress = true;
    gantt.config.grid_width = 580;
    gantt.config.columns = [
        {name:"text",label:"Task name",width:"350",align: "left", tree:true },
        {name:"start_date",label:"Start time",align: "center" },
        {name:"end_date",label:"End time",align: "center" },
        {name:"duration",label:"Duration",width:"50",align: "center" }
    ];
    gantt.templates.task_cell_class = function(task, date){
        if(!gantt.isWorkTime(date))
            return "week_end";
        return "";
    };
    gantt.templates.scale_cell_class = function(date){
        if(!gantt.isWorkTime(date))
            return "week_end";
        return "";
    };
    gantt.templates.date_grid = function(start_date){
        return gantt.date.date_to_str(gantt.config.date_grid)(start_date);
    };
    gantt.config.date_grid = "%d-%m-%Y";
    gantt.open();
    var tasks = {<?php echo $tasks;?>};

    gantt.init("gantt_here");
    gantt.parse (tasks);
    gantt.attachEvent("onAfterTaskUpdate", function(id,item){
        var links = gantt.serialize().links;                             //returns all links
        var childcource='';
        for(var i=0;i<links.length; i++){                              //goes over all links
            if ( (links[i].source == id)&& (links[i].type==1))
            {
                var linkId = links[i].target;
                var task = gantt.getTask(linkId);
                task.start_date=item.start_date;
                task.end_date=gantt.calculateEndDate(item.start_date, task.duration);
                task.duration=task.duration;
                task.parent=task.parent;
                gantt.updateTask(task.id);
                childcource=task.id
            }
            else if((links[i].source == id)&& (links[i].type==0))
            {
                var linkId = links[i].target;
                var task = gantt.getTask(linkId);
                task.start_date=gantt.calculateEndDate(item.start_date, item.duration);
                task.end_date=gantt.calculateEndDate(task.start_date, task.duration);	;
                task.duration=task.duration;
                task.parent=task.parent;
                gantt.updateTask(task.id);
                childcource=task.id
            }
            if((links[i].source == childcource) && (links[i].type==0))
            {

                var linkId = links[i].target;
                var task = gantt.getTask(linkId);
                var taskparent = gantt.getTask(childcource);
                task.start_date=gantt.calculateEndDate(taskparent.start_date, taskparent.duration);
                task.end_date=gantt.calculateEndDate(task.start_date, task.duration);
                task.duration=task.duration;
                task.parent=task.parent;
                gantt.updateTask(task.id);
                childcource=task.id
            }
        };
    });
    gantt.attachEvent("onAfterLinkAdd", function(id,item){


        if(item.type==0)
        {

            var sourcetask = gantt.getTask(item.source);
            var targetast=gantt.getTask(item.target);
            targetast.start_date=gantt.calculateEndDate(sourcetask.start_date, sourcetask.duration);
            targetast.end_date=gantt.calculateEndDate(targetast.start_date, targetast.duration);
            targetast.duration=targetast.duration;
            gantt.updateTask(targetast.id);
        }
        else if(item.type==1)
        {
            var sourcetask = gantt.getTask(item.source);
            var targetast=gantt.getTask(item.target);
            targetast.start_date=gantt.calculateEndDate(sourcetask.start_date);
            targetast.end_date=gantt.calculateEndDate(targetast.start_date, targetast.duration);
            targetast.duration=targetast.duration;
            gantt.updateTask(targetast.id);
        }
        else if(item.type==2)
        {
            var sourcetask = gantt.getTask(item.source);
            var targetast=gantt.getTask(item.target);
            if(sourcetask.end_date>targetast.end_date)
            {
                var startdate=GetFormatedDate(sourcetask.end_date,targetast.duration);
                targetast.start_date=gantt.calculateEndDate(startdate);
                targetast.end_date=gantt.calculateEndDate(sourcetask.end_date);
                targetast.duration=targetast.duration;
                gantt.updateTask(targetast.id);
            }
            else if(sourcetask.end_date<targetast.end_date)
            {
                var startdate=GetFormatedDate(targetast.end_date,sourcetask.duration);
                sourcetask.start_date=gantt.calculateEndDate(startdate);
                sourcetask.end_date=gantt.calculateEndDate(targetast.end_date);
                sourcetask.duration=sourcetask.duration;
                gantt.updateTask(sourcetask.id);
            }
            else
            {

            }
        }
        else if(item.type==3)
        {
            var sourcetask = gantt.getTask(item.source);
            var targetast=gantt.getTask(item.target);
            var startdate=GetFormatedDate(sourcetask.start_date,targetast.duration);
            targetast.start_date=gantt.calculateEndDate(startdate);
            targetast.end_date=gantt.calculateEndDate(sourcetask.start_date);
            targetast.duration=targetast.duration;
            gantt.updateTask(targetast.id);
        }

    });
    gantt.attachEvent("onAfterLinkDelete", function(id,item){

        var sourcetask = gantt.getTask(item.source);
        var targetast=gantt.getTask(item.target);
        targetast.start_date=gantt.calculateEndDate(sourcetask.start_date);
        targetast.end_date=gantt.calculateEndDate(targetast.start_date, targetast.duration);
        targetast.duration=targetast.duration;
        gantt.updateTask(targetast.id);
    });
    gantt.attachEvent("onTaskDblClick", function(id,e){
        return false;
    });
    gantt.attachEvent("onTaskDrag", function(id, mode, task, original){

        return false;
    });
    var dp=new dataProcessor("<?php echo Yii::app()->request->baseUrl; ?>/workgroups/Updatechart");
    //var dp=new dataProcessor("<?php echo Yii::app()->request->baseUrl; ?>/codebase/data.php");
    dp.init(gantt);
    var func = function(e) {
        e = e || window.event;
        var el = e.target || e.srcElement;
        var value = el.value;
        setScaleConfig(value);
        gantt.render();
    };

    var els = document.getElementsByName("scale");
    for (var i = 0; i < els.length; i++) {
        els[i].onclick = func;
    }
    function GetFormatedDate(date,duration)
    {
        var dateOffset = (24*60*60*1000) * duration; //5 days
        var myDate = new Date(date);
        myDate.setTime(myDate.getTime() - dateOffset);
        return myDate;
    }
    function checkDate(date)
    {
        var retval;
        var offdaysarr=[<?php echo $offdays;?>];
        var fondval=jQuery.inArray( date, offdaysarr )
        if(fondval!=-1)
        {
            retval='yes'
        }
        else
        {
            retval='no';
        }
        return retval;
    }
</script>