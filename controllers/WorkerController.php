<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Worker;
use app\models\Position;
use yii\helpers\BaseJson;

class WorkerController extends Controller
{
    
    public $enableCsrfValidation = false;
    
    private $error = "";
    
    private $controllerName = "worker";
    
    private $primaryKeyName = "worker_id";
    
    private function setError($errorArray) {
        $this->error = implode(", ", $errorArray);
    }
    
    private function getError() {
        return $this->error;
    }
    
    private function getList() {
       return Worker::find()->with("position")->all(); 
    }
    
    private function getPositions() {
       return Position::find()->all(); 
    }
    
    private function sendInJson($data) {
        $json = BaseJson::encode($data);
        $res = Yii::$app->getResponse();
        $res->format = Response::FORMAT_JSON;
        $res->data = $json;
        $res->send();
    }
    
    public function actionList() {
        $type = '';
        if (isset($_REQUEST['type'])) {
            $type = $_REQUEST['type'];
        }
        if ($type === 'json') {
            $list = $this->getList();
            $this->sendInJson($list);
        } else {
            $list = $this->getList(); 
            return $this->render("list", ["list" => $list]);
        }
    }
    
    public function actionAdd() {
        $error = "";
        if (isset($_REQUEST['submit'])) {
            $name = $_REQUEST['name'];
            $paramsArray = $_REQUEST['Model'];
            $ok = $this->add($paramsArray);
            if (!$ok) {
                $error = $this->getError();
            }
            if ($ok) {
                $this->redirect([$this->controllerName . "/list"]);
            }
        }
        return $this->render("add", ["error" => $error, "positions" => $this->getPositions()]);
    }
    
    public function actionChange() {
        $error = "";
        $id = $_REQUEST[$this->primaryKeyName];
        $model = Worker::findOne($id);
        if (isset($_REQUEST['submit'])) {
            $paramsArray = $_REQUEST["Model"];
            $ok = $this->change($model, $paramsArray);
            if (!$ok) {
                $error = $this->getError();
            }
            if ($ok) {
                $this->redirect([$this->controllerName . "/list"]);
            }
        }
        $positions = $this->getPositions();
        return $this->render("change", ["error" => $error, "model" => $model, "positions" => $positions]);
    }
    
    public function actionDelete() {
        $id = $_REQUEST[$this->primaryKeyName];
        if (isset($id)) {
            $model = Worker::findOne($id);
            $code = $model->delete();
            $this->redirect([$this->controllerName . "/list"]);
        }
    }
    
    private function add($paramsArray) {
        $model = new Worker();
        $model->setAttributes($paramsArray, false);
        $ok = $model->save();
        if (!$ok) {
            $this->setError($model->getErrorSummary(true));
        }
        return $ok;
    }
    
    private function change($model, $paramsArray) {
        $model->setAttributes($paramsArray, false);
        $ok = $model->save();
        if (!$ok) {
            $this->setError($model->getErrorSummary(true));
        }
        return $ok;
    }
    
    
}