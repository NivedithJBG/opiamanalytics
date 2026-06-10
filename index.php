<?php

// Override SCRIPT_FILENAME before Yii2 boots.
// Yii2 derives @webroot from this value; pointing it to production/web/index.php
// ensures @webroot = production/web/ so all asset, CSS and JS URLs resolve correctly.
$_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/production/web/index.php';

// Disable debug mode for production. For a dev/test instance, change these to:
//   define('YII_DEBUG', true);
//   define('YII_ENV', 'dev');
defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_ENV') or define('YII_ENV', 'prod');

// Delegate to the real Yii2 entry point.
// __DIR__ inside that file resolves to production/web/ as expected, so all
// relative paths (vendor/autoload.php, config/web.php, etc.) remain correct.
require __DIR__ . '/production/web/index.php';
