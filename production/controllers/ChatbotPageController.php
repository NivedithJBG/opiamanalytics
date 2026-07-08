<?php
namespace app\controllers;

use yii\web\Controller;

class ChatbotPageController extends Controller
{
    public function behaviors()
    {
        return [];
    }

    public function actionIndex()
    {
        return $this->renderPartial('index');
    }
}
