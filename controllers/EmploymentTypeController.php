<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Employment_type;
use app\helpers\Output;

class EmploymentTypeController extends Controller
{
    
    private $error = "";
    
    private $controllerName = "employment-type";
    
    private $primaryKeyName = "employment_type_id";
    
    private function setError($errorArray) {
        $this->error = implode(", ", $errorArray);
    }
    
    private function getError() {
        return $this->error;
    }
    
    private function getList($orderColumn = '', $orderType = 'ASC') {
       $sql = "SELECT * FROM employment_type"; 
       if ($orderColumn) {
           $sql .= ' order by ' . $orderColumn . ' ' . $orderType;
       }
       return Employment_type::findBySql($sql)->all();
       //return Employment_type::find()->all(); 
    }
    
    public function actionList() {
        
        $orderColumn = Yii::$app->request->get("orderColumn");
        if ($orderColumn) {
            $oldOrderType = Yii::$app->session->get("employment_type_orderType");
            $oldOrderColumn = Yii::$app->session->get("employment_type_orderColumn");
            $orderType = 'ASC';
            if ($oldOrderType === 'ASC' && $oldOrderColumn === $orderColumn) {
                $orderType = 'DESC';
            }
            $list = $this->getList($orderColumn, $orderType);
            Yii::$app->session->set("employment_type_orderType", $orderType);
            Yii::$app->session->set("employment_type_orderColumn", $orderColumn);
        } else {
            $list = $this->getList();
            Yii::$app->session->remove("employment_type_orderType");
            Yii::$app->session->remove("employment_type_orderColumn");
        }
        
        //$list = $this->getList(); 
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
        $model = Employment_type::findOne($id);
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
            $ajaxResult['html'] = $this->renderPartial("change", ["error" => $error, "model" => $model]);
            Output::sendInJson($ajaxResult);
        } else {
            return $this->render("change", ["error" => $error, "model" => $model]);
        }
    }
    
    public function actionDelete() {
        $id = $_REQUEST[$this->primaryKeyName];
        if (isset($id)) {
            $model = Employment_type::findOne($id);
            $code = $model->delete();
            $this->redirect([$this->controllerName . "/list"]);
        }
    }
    
    private function add($paramsArray) {
        $model = new Employment_type();
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