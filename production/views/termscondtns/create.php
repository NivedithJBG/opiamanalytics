<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TermsCondtns */

$this->title = 'Create Terms Condtns';
$this->params['breadcrumbs'][] = ['label' => 'Terms Condtns', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="terms-condtns-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
