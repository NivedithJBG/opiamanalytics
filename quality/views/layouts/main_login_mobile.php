<!DOCTYPE html>
<html lang="en" data-sidebar-color="dark" data-topbar-color="light" data-sidebar-view="default">

    <head>
        <meta charset="utf-8">
        <title>Performance Pad | Login</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Opiam Analytics" name="author">

        <!-- App favicon -->
        <link rel="shortcut icon" href="<?php echo Yii::$app->request->baseUrl; ?>/mob_theme/assets/images/favicon.ico">

        <!-- App css -->
        <link href="<?php echo Yii::$app->request->baseUrl; ?>/mob_theme/assets/css/theme.css" rel="stylesheet" type="text/css">
        <link href="<?php echo Yii::$app->request->baseUrl; ?>/mob_theme/assets/css/icons.min.css" rel="stylesheet" type="text/css">
        <link href="<?php echo Yii::$app->request->baseUrl; ?>/mob_theme/assets/css/custom.css" rel="stylesheet" type="text/css">

        <!-- Head Js -->
        <script src="<?php echo Yii::$app->request->baseUrl; ?>/mob_theme/assets/js/head.js"></script>
    </head>

    <body class="bg-primary h-screen w-screen flex justify-center items-center">
        <div class="2xl:w-1/4 lg:w-1/3 md:w-1/2 w-full">
            <?= $content ?>
        </div>
    </body>

</html>