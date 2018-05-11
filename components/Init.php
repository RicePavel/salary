<?php

namespace app\components;

use Yii;
use yii\helpers\Url;

class Init extends \yii\base\Component {
    
    public function init() {
        $loginUrl = Url::to(["/site/login"]);
        if (Yii::$app->getUser()->isGuest &&
                Yii::$app->getRequest()->url !== $loginUrl) {
            Yii::$app->getResponse()->redirect($loginUrl);
        }
        parent::init();
    }
    
}

