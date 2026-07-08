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
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        Yii::$app->response->headers->set('Content-Type', 'text/html; charset=utf-8');
        return $this->renderFile('@app/views/chatbot/index.php');
    }
}
