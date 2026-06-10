<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TermsCondtns */

$this->title = 'Update Terms Condtns: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Terms Condtns', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="terms-condtns-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
