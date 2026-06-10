<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\Url;
use amnah\yii2\user\models\User;

class SamlController extends Controller {

    // Remove CSRF protection
    public $enableCsrfValidation = false;

    public function actions() {
        //$session = Yii::$app->session;
        return [
            'login' => [
                'class' => 'asasmoyo\yii2saml\actions\LoginAction',
                'returnTo' => Yii::$app->request->referrer
            ],
            'acs' => [
                'class' => 'asasmoyo\yii2saml\actions\AcsAction',
                'successCallback' => [$this, 'callback'],
                'successUrl' => Url::to('@web/projectsmain/index'),
            ],
            'metadata' => [
                'class' => 'asasmoyo\yii2saml\actions\MetadataAction'
            ],
            'logout' => [
                'class' => 'asasmoyo\yii2saml\actions\LogoutAction',
                //'returnTo' => Url::to('site/bye'),
                'returnTo' => Url::to('@web/saml/login'),
                'parameters' => [],
                'nameId' => Yii::$app->session->get('nameId'),
                'sessionIndex' => Yii::$app->session->get('sessionIndex'),
                'stay' => false,
                'nameIdFormat' => null,
                'nameIdNameQualifier' => Yii::$app->session->get('nameIdNameQualifier'),
                'nameIdSPNameQualifier' => Yii::$app->session->get('nameIdSPNameQualifier'),
                'logoutIdP' => true, // if you don't want to logout on idp
            ],
            'sls' => [
                'class' => 'asasmoyo\yii2saml\actions\SlsAction',
                //'successUrl' => Url::to('site/bye'),
                'successUrl' => Url::to('@web/saml/login'),
                'logoutIdP' => true, // if you don't want to logout on idp
            ]
        ];
    }
    
    public function callback($param) {
        // do something
        $username = $param['nameId'];
        $identity = User::findOne(['email' => $username]);
        //echo $username; exit;
        if($identity){
            // logs in the user
            Yii::$app->session->set('Saml', 'user');
            Yii::$app->user->login($identity);
        }
        else{
            //echo Url::to('@web/saml/logout');
            return $this->redirect(['saml/logout']);
        }
        // if (isset($_POST['RelayState'])) {
        // $_POST['RelayState'] - should be returnUrl from login action
        // }
    }

}