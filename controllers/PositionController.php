<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Position;
use app\helpers\Output;

class PositionController extends Controller
{
    
    public $enableCsrfValidation = false;
    
    private $error = "";
    
    private $controllerName = "position";
    
    private $primaryKeyName = "position_id";
    
    private function setError($errorArray) {
        $this->error = implode(", ", $errorArray);
    }
    
    private function getError() {
        return $this->error;
    }
    
    private function getList() {
       return Position::find()->all(); 
    }
    
    public function actionList() {
        $list = $this->getList(); 
        return $this->render("list", ["list" => $list]);
    }
    
    public function actionAdd() {
        $error = "";
        $type = (isset($_REQUEST['type']) ? $_REQUEST['type'] : '');
        $ajaxResult = ['ok' => true, 'html' => ''];
        if (isset($_REQUEST['submit'])) {
            $paramsArray = $_REQUEST['Model'];
            $ok = $this->add($paramsArray);
            if (!$ok) {
                $error = $this->getError();
            }
            $ajaxResult['ok'] = $ok;
            if ($ok) {
                if ($type === 'ajax') {
                   Output::sendInJson($ajaxResult);
                } else {
                    $this->redirect([$this->controllerName . "/list"]);
                }
            }
        }
        if ($type === 'ajax') {
            $ajaxResult['html'] = $this->renderPartial("add", ["error" => $error]);
            Output::sendInJson($ajaxResult);
        } else {
            return $this->render("add", ["error" => $error]);
        }
    }
    
    public function actionChange() {
        $error = "";
        $id = $_REQUEST[$this->primaryKeyName];
        $model = Position::findOne($id);
        $type = (isset($_REQUEST['type']) ? $_REQUEST['type'] : '');
        $ajaxResult = ['ok' => true, 'html' => ''];
        if (isset($_REQUEST['submit'])) {
            $paramsArray = $_REQUEST["Model"];
            $ok = $this->change($model, $paramsArray);
            if (!$ok) {
                $error = $this->getError();
            }
            $ajaxResult['ok'] = $ok;
            if ($ok) {
                if ($type === 'ajax') {
                   Output::sendInJson($ajaxResult);
                } else {
                    $this->redirect([$this->controllerName . "/list"]);
                }
            }
        } 
        if ($type === 'ajax') {
           $ajaxResult['html'] =  $this->renderPartial("change", ["error" => $error, "model" => $model]);
           Output::sendInJson($ajaxResult);
        } else {
           return $this->render("change", ["error" => $error, "model" => $model]); 
        }
    }
    
    public function actionDelete() {
        $id = $_REQUEST[$this->primaryKeyName];
        if (isset($id)) {
            $model = Position::findOne($id);
            $code = $model->delete();
            $this->redirect([$this->controllerName . "/list"]);
        }
    }
    
    private function add($paramsArray) {
        $model = new Position();
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