<!DOCTYPE html>
<html lang="en" data-sidebar-color="dark" data-topbar-color="light" data-sidebar-view="default">

<head>
    <meta charset="utf-8">
    <title>Performance Pad | Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Opiam Analytics" name="author">

    <!-- App favicon -->
    <link rel="shortcut icon" href="<?php echo Yii::$app->request->baseUrl; ?>/mob_theme/assets/images/favicon.ico">

    <!-- App css -->
    <link href="<?php echo Yii::$app->request->baseUrl; ?>/mob_theme/assets/css/theme.css" rel="stylesheet" type="text/css">
    <link href="<?php echo Yii::$app->request->baseUrl; ?>/mob_theme/assets/css/icons.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/jquery-ui.css" rel="stylesheet">
    
    <link href="<?php echo Yii::$app->request->baseUrl; ?>/mob_theme/assets/css/custom.css" rel="stylesheet" type="text/css">

    <!-- Head Js -->
    <script src="<?php echo Yii::$app->request->baseUrl; ?>/mob_theme/assets/js/head.js"></script>
</head>

<body>

    <div class="app-wrapper">



        <!-- Start Page Content here -->
        <div class="app-content">

            <!-- Topbar Start -->
            <header class="app-header flex items-center px-5 gap-2">
   
                <!-- Brand Logo -->
                <button class="nav-link p-2 waves-effect me-auto">
                    <img src="<?php echo Yii::$app->request->baseUrl; ?>/mob_theme/assets/images/logo.png" class="logo-dark h-8" alt="logo">
                </button>


               <!-- Report Dashboard Button -->
               <!-- <div class="md:flex hidden">
                    <button type="button" class="nav-link p-2 waves-effect">
                        <span class="sr-only">Report Dashboard</span>
                        <span class="flex items-center justify-center h-6 w-6">
                            <i class="ph ph-chart-pie text-2xl"></i>
                        </span>
                    </button>
                </div> -->

                <!-- Performance Dashboard Button -->
                <!-- <div class="md:flex hidden">
                    <button type="button" class="nav-link p-2 waves-effect">
                        <span class="sr-only">Performance Dashboard</span>
                        <span class="flex items-center justify-center h-6 w-6">
                            <i class="ph ph-chart-line-up text-2xl"></i>
                        </span>
                    </button>
                </div> -->

                <!-- Gantt Chart Button -->
                <!-- <div class="md:flex hidden">
                    <button  type="button" class="nav-link p-2 waves-effect">
                        <span class="sr-only">Gantt Chart</span>
                        <span class="flex items-center justify-center h-6 w-6">
                            <i class="ph ph-chart-bar text-2xl"></i>
                        </span>
                    </button>
                </div> -->

                <!-- Mobile Dashbord Button -->
                <!-- <div class="sm:hidden">
                    <button data-fc-type="dropdown" data-fc-placement="bottom-end" type="button" class="nav-link p-2 waves-effect">
                        <span class="sr-only">Dashboard</span>
                        <span class="flex items-center justify-center h-6 w-6">
                            <i class="ph ph-chart-bar text-2xl"></i>
                        </span>
                    </button>
                    <div class="fc-dropdown fc-dropdown-open:opacity-100 hidden opacity-0 w-40 z-50 transition-[margin,opacity] duration-300 mt-2 bg-white shadow-lg border rounded-lg p-2">
                        <a class="flex items-center py-2 px-3 rounded-md text-sm text-gray-800 hover:bg-gray-100" href="#">
                            Report Dashboard
                        </a>
                        <a class="flex items-center py-2 px-3 rounded-md text-sm text-gray-800 hover:bg-gray-100" href="#">
                            Performance Dashboard
                        </a>
                        <a class="flex items-center py-2 px-3 rounded-md text-sm text-gray-800 hover:bg-gray-100" href="#">
                            Gantt Chart
                        </a>
                    </div>
                </div> -->

                <!-- Fullscreen Toggle Button -->
                <div class="md:flex hidden">
                    <button data-toggle="fullscreen" type="button" class="nav-link p-2 waves-effect">
                        <span class="sr-only">Fullscreen Mode</span>
                        <span class="flex items-center justify-center h-6 w-6">
                            <i class="ph ph-arrows-out text-2xl"></i>
                        </span>
                    </button>
                </div>

                <!-- Profile Dropdown Button -->
                <div class="relative">
                    <button data-fc-type="dropdown" data-fc-placement="bottom-end" type="button" class="nav-link flex items-center gap-2.5 waves-effect p-2">
                        <img src="<?php echo Yii::$app->request->baseUrl; ?>/mob_theme/assets/images/users/avatar-6.jpg" alt="user-image" class="rounded-full h-8 w-8">
                        <span class="md:flex items-center hidden">
                            <span class="font-medium text-base">Jamie D.</span>
                            <i class='ph ph-chevron-down'></i>
                        </span>
                    </button>
                    <div class="fc-dropdown fc-dropdown-open:opacity-100 hidden opacity-0 w-40 z-50 transition-[margin,opacity] duration-300 mt-2 bg-white shadow-lg border rounded-lg p-2">
                        <a class="flex items-center py-2 px-3 rounded-md text-sm text-gray-800 hover:bg-gray-100" href="#">
                            Log Out
                        </a>
                    </div>
                </div>
            </header>
            <!-- Topbar End -->



            <?= $content ?>




             <!-- Footer Start -->
            <footer class="footer h-16 flex items-center px-6 bg-white border-t border-gray-200 mt-auto">
                <div class="flex md:justify-between justify-center w-full gap-4">
                    <div>
                        <p class="text-sm font-medium"><script>document.write(new Date().getFullYear())</script> © Opiam Analytics</p>
                    </div>
                    <div class="md:flex hidden gap-2 item-center md:justify-end">
                        <p class="text-sm font-medium">Design &amp; Develop by <a href="#" class="text-primary">Opiam Analytics</a></p>
                    </div>
                </div>
            </footer>
            <!-- Footer End -->

        </div>
        <!-- End Page content -->

    </div>

    <!-- Plugin Js -->
    <script src="<?php echo Yii::$app->request->baseUrl; ?>/mob_theme/assets/libs/jquery/jquery.min.js"></script>
    <script src="<?php echo Yii::$app->request->baseUrl; ?>/mob_theme/assets/libs/simplebar/simplebar.min.js"></script>
    <script src="<?php echo Yii::$app->request->baseUrl; ?>/mob_theme/assets/libs/node-waves/waves.min.js"></script>
    <script src="<?php echo Yii::$app->request->baseUrl; ?>/mob_theme/assets/libs/@frostui/tailwindcss/frostui.js"></script>

    <script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/jquery-ui.js"></script>

    <!-- App Js -->
    <script src="<?php echo Yii::$app->request->baseUrl; ?>/mob_theme/assets/js/app.js"></script>

    <script src="<?php echo Yii::$app->request->baseUrl; ?>/mob_theme/assets/js/custom.js"></script>
</body>

</html>