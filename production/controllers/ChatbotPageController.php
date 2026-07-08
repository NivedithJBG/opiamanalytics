<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;

class ChatbotPageController extends Controller
{
    public $layout = false;

    public function behaviors()
    {
        return [];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }
}
