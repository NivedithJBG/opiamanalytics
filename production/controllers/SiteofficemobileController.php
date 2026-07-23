<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use app\models\ProjuserSelection;

class SiteofficemobileController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    ['allow' => true, 'actions' => ['login'], 'roles' => ['?']],
                    ['allow' => true, 'roles' => ['@']],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionMobile()
    {
        $this->layout = '@app/views/layouts/mobile';
        $uid = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        $projectid = $projuser ? $projuser->projectid : null;
        $db = Yii::$app->db;
        $project = $projectid
            ? $db->createCommand("SELECT Name FROM projects WHERE Project_Id=:pid", [':pid' => $projectid])->queryOne()
            : null;
        $projectName = $project ? $project['Name'] : 'No Project Selected';
        return $this->render('mobile', ['projectName' => $projectName]);
    }
}
