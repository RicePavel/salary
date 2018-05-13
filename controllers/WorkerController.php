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
use app\models\Unit;
use app\models\Work_type;
use yii\helpers\BaseJson;
use app\helpers\Output;
use yii\db\ActiveRecord;

class WorkerController extends Controller
{
    
    private $error = "";
    
    private $controllerName = "worker";
    
    private $primaryKeyName = "worker_id";
    
    private function setError($errorArray) {
        $this->error = implode(", ", $errorArray);
    }
    
    private function getError() {
        return $this->error;
    }
    
    private function getList($orderColumn = '', $orderType = 'ASC') {
       //return Worker::find()->with("position")->with("unit")->all(); 
        
       $sql = "SELECT * FROM worker left join unit on worker.unit_id = unit.unit_id left join position on worker.position_id = position.position_id "; 
       if ($orderColumn) {
           $sql .= ' order by ' . $orderColumn . ' ' . $orderType;
       }
       return Worker::findBySql($sql)->with('position')->with('unit')->all(); 
    }
    
    private function getPositions() {
       return Position::find()->all(); 
    }
    
    private function getUnits() {
        return Unit::find()->all();
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
            
            $orderColumn = Yii::$app->request->get("orderColumn");
            if ($orderColumn) {
                $oldOrderType = Yii::$app->session->get("worker_orderType");
                $oldOrderColumn = Yii::$app->session->get("worker_orderColumn");
                $orderType = 'ASC';
                if ($oldOrderType === 'ASC' && $oldOrderColumn === $orderColumn) {
                    $orderType = 'DESC';
                }
                $list = $this->getList($orderColumn, $orderType);
                Yii::$app->session->set("worker_orderType", $orderType);
                Yii::$app->session->set("worker_orderColumn", $orderColumn);
            } else {
                $list = $this->getList();
                Yii::$app->session->remove("worker_orderType");
                Yii::$app->session->remove("worker_orderColumn");
            }
            
            return $this->render("list", ["list" => $list]);
        }
    }
    
    public function actionAdd() {
        $error = "";
        $type = "";
        if (isset($_REQUEST['type'])) {
            $type = $_REQUEST['type'];
        }
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
            $ajaxResult['html'] = $this->renderPartial("add", ["error" => $error, "positions" => $this->getPositions(), "units" => $this->getUnits()]);
            Output::sendInJson($ajaxResult);
        } else {
            return $this->render("add", ["error" => $error, "positions" => $this->getPositions(), "units" => $this->getUnits()]);
        }
    }
    
    public function actionChange() {
        $error = "";
        
        $id = $_REQUEST[$this->primaryKeyName];
        $model = Worker::findOne($id);
        $type = (isset($_REQUEST['type']) ? $_REQUEST['type'] : '');
        $ajaxResult = ['ok' => true, 'html' => ''];
        if (isset($_REQUEST['submit'])) {
            $paramsArray = $_REQUEST["Model"];
            $ok = $this->change($model, $paramsArray);
            $ajaxResult['ok'] = $ok;
            if (!$ok) {
                $error = $this->getError();
            }
            if ($ok) {
                if ($type === 'ajax') {
                   Output::sendInJson($ajaxResult);
                } else {
                    $this->redirect([$this->controllerName . "/list"]);
                }
            }
        }
        $positions = $this->getPositions();
        if ($type === 'ajax') {
            $ajaxResult['html'] = $this->renderPartial("change", ["error" => $error, "model" => $model, "positions" => $positions, "units" => $this->getUnits()]);
            Output::sendInJson($ajaxResult);
        } else {
            return $this->render("change", ["error" => $error, "model" => $model, "positions" => $positions, "units" => $this->getUnits()]);
        }
    }
    
    public function actionDelete() {
        $id = $_REQUEST[$this->primaryKeyName];
        if (isset($id)) {
            $model = Worker::findOne($id);
            $code = $model->delete();
            $this->redirect([$this->controllerName . "/list"]);
        }
    }
    
    private function add(array $paramsArray) {
        $model = new Worker();
        $model->setAttributes($paramsArray, false);
        $ok = $model->save();
        if (!$ok) {
            $this->setError($model->getErrorSummary(true));
        }
        return $ok;
    }
    
    private function change(ActiveRecord $model, array $paramsArray) {
        $model->setAttributes($paramsArray, false);
        $ok = $model->save();
        if (!$ok) {
            $this->setError($model->getErrorSummary(true));
        }
        return $ok;
    }
    
    
}