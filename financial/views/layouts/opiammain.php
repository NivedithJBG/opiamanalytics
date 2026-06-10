<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!--<meta name="viewport" content="width=device-width, initial-scale=1">-->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <!--<link rel="shortcut icon" href="../../assets/ico/favicon.ico">-->

    <title>GeoTech</title>

    <!-- Bootstrap core CSS -->
    <link href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap.min.css?v=2" rel="stylesheet">
    <link href="<?php echo Yii::app()->request->baseUrl; ?>/css/cover.css" rel="stylesheet">
    <link href="<?php echo Yii::app()->request->baseUrl; ?>/css/jumbotron-narrow.css" rel="stylesheet">
    <link href="<?php echo Yii::app()->request->baseUrl; ?>/css/jquery.autocomplete.css" rel="stylesheet">
    <link href="<?php echo Yii::app()->request->baseUrl; ?>/css/menu.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/normalize.css">
    <link href="<?php echo Yii::app()->request->baseUrl; ?>/cssnew/icons.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/style.css">
    <link href='https://fonts.googleapis.com/css?family=Montserrat:400,500,700' rel='stylesheet' type='text/css'>
    <!--<link rel="stylesheet" href="//code.jquery.com/ui/1.11.0/themes/smoothness/jquery-ui.css">-->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
    <link href="<?php echo Yii::app()->request->baseUrl; ?>/css/magicsuggest.css" rel="stylesheet">
    <link href="<?php echo Yii::app()->request->baseUrl; ?>/cssnew/style.css?v=1.18" rel="stylesheet">
    <link href="<?php echo Yii::app()->request->baseUrl; ?>/cssnew/ipad.css?v=1.38" rel="stylesheet">
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/common.js?v=1.6"></script>
    


    <!--<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">-->

    <!-- Custom styles for this template -->


    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]>
    <!--<script src="../../assets/js/ie8-responsive-file-warning.js"></script>-->
    <![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>

    <![endif]-->
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery-1.9.1.min.js" ></script>
    <script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.autocomplete.js" type="text/javascript"></script>
    <script src="<?php echo Yii::app()->request->baseUrl; ?>/js/magicsuggest.js" type="text/javascript"></script>

    <script>
        $(function() {
            var pull 		= $('#pull');
            menu 		= $('nav ul');
            menuHeight	= menu.height();

            $(pull).on('click', function(e) {
                e.preventDefault();
                menu.slideToggle();
            });

            $(window).resize(function(){
                var w = $(window).width();
                if(w > 320 && menu.is(':hidden')) {
                    menu.removeAttr('style');
                }
            });
            /*$( "#datepicker" ).datepicker({
                changeMonth: true,
                changeYear: true
            });*/
        });
    </script>

    <style>
        label {
            display: inline-block;
            width: 5em;
        }
    </style>
    <script src="//code.jquery.com/ui/1.11.0/jquery-ui.js"></script>

    <!--<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>-->
</head>

<body class="test">
<a href="<?php echo Yii::app()->createUrl('site/index')?>" class="homeurl"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/logo.png?v=1" id="logoimage"/></a>
<div class="header" style="border:0px">

    

</div>
<div class="container-fluid">

    <?php if(isset($this->breadcrumbs)):?>
        <?php $this->widget('zii.widgets.CBreadcrumbs', array(
        'links'=>$this->breadcrumbs,
    )); ?><!-- breadcrumbs -->
    <?php endif?>
    <div class="jumbotron">

    <?php echo $content; ?>
    </div>

    <!--<div class="footer">-->
    <!--    <p>&copy; Geotech 2017</p>-->
    <!--</div>-->
    <input type="hidden" id="indexfield" value="1" >
</div> <!-- /container -->


<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script type="text/javascript">
    $(function(){
        var type = window.location.hash.substr(1);
        //alert(type)
        setTimeout(function() {
            $('#'+type).trigger('click');
        },1000);
    });
</script>
<script type="text/javascript" >
    $(document).ready(function()
    {
        $("#notificationLink").click(function()
        {
            $("#notificationContainer").fadeToggle(300);
            $("#notification_count").fadeOut("slow");
            var item='all';
            var userid='<?php echo Yii::app()->user->id;?>';
            $.ajax({
                type: 'POST',
                url: '../../Task/changestatus',
                data: { item: item,userid:userid}
            });
            return false;

        });

        $("#settings").click(function()
        {
            $("#settingsContainer").fadeToggle(300);

        });
        //Document Click hiding the popup
        $(document).click(function()
        {
            $("#notificationContainer").hide();
            //$("#settingsContainer").hide();
        });
        $('#clearnotif').click(function() {
            //$("#notificationContainer").fadeOut(300);
            var userid='<?php echo Yii::app()->user->id;?>';
            $.ajax({
                type: 'POST',
                url: '../site/clearnotifications',
                data: { userid:userid},
                dataType: "json",
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#notificationsBody').html('');
                    }

                }
            });
        });

        //Popup on click
        /*$("#notificationContainer").click(function()
         {
         return false;
         });*/

    });
    $(document).on('click','.readnotification',function(){
        var item=$(this).data('id');
        var userid='<?php echo Yii::app()->user->id;?>';
        $.ajax({
            type: 'POST',
            url: '../../Task/changestatus',
            data: { item: item,userid:userid}
        })
    });
</script>
</body>

</html>
