<?php
use yii\helpers\Html;
$this->beginPage();
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="Progress Report">
<meta name="theme-color" content="#334155">
<title><?= Html::encode($this->title ?: 'Progress Report') ?></title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
<?php $this->head() ?>
<style>
*{box-sizing:border-box;margin:0;padding:0;-webkit-tap-highlight-color:transparent}
body{font-family:'Inter',sans-serif;background:#f4f5f7;color:#1e293b;min-height:100vh;overflow-x:hidden}
</style>
</head>
<body>
<?php $this->beginBody() ?>
<?= $content ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
