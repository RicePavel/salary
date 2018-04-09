<?php

namespace app\helpers;

use yii\helpers\BaseJson;
use yii\web\Response;
use Yii;

class Output {
    
    public static function sendInJson($data) {
        $json = BaseJson::encode($data);
        $res = Yii::$app->getResponse();
        $res->format = Response::FORMAT_JSON;
        $res->data = $json;
        $res->send();
    }
    
}

