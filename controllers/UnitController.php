<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Unit;
use app\helpers\Output;

class UnitController extends Controller
{
    
    private $error = "";
    
    private $controllerName = "unit";
    
    private $primaryKeyName = "unit_id";
    
    private function setError($errorArray) {
        $this->error = implode(", ", $errorArray);
    }
    
    private function getError() {
        return $this->error;
    }
    
    private function getList($orderColumn = '', $orderType = 'ASC') {
       
       $sql = "SELECT * FROM unit"; 
       if ($orderColumn) {
           $sql .= ' order by ' . $orderColumn . ' ' . $orderType;
       }
       $units = Unit::findBySql($sql)->all();
        
       //$units = Unit::find()->all();
       
       $unitsArray = [];
       foreach ($units as $unit) {
           $unitsArray[$unit->unit_id] = $unit;
       }
       foreach ($unitsArray as $unitId => $unit) {
           $parentId = $unit->parent_id;
           if ($parentId !== null) {
               if (isset($unitsArray[$parentId])) {
                   $unitsArray[$parentId]->addChild($unit);
               }
           }
       }
       $units = [];
       foreach ($unitsArray as $unitId => $unit) {
           if ($unit->parent_id === null) {
               $units[] = $unit;
           }
       }
       return $units; 
    }
    
    public function actionList() {
        //$list = $this->getList(); 
        
        $orderColumn = Yii::$app->request->get("orderColumn");
        if ($orderColumn) {
            $oldOrderType = Yii::$app->session->get("unit_orderType");
            $oldOrderColumn = Yii::$app->session->get("unit_orderColumn");
            $orderType = 'ASC';
            if ($oldOrderType === 'ASC' && $oldOrderColumn === $orderColumn) {
                $orderType = 'DESC';
            }
            $list = $this->getList($orderColumn, $orderType);
            Yii::$app->session->set("unit_orderType", $orderType);
            Yii::$app->session->set("unit_orderColumn", $orderColumn);
        } else {
            $list = $this->getList();
            Yii::$app->session->remove("unit_orderType");
            Yii::$app->session->remove("unit_orderColumn");
        }
        
        return $this->render("list", ["list" => $list]);
    }
    
    public function actionAdd() {
        $error = "";
        $type = "";
        $ajaxResult = ['ok' => true, 'html' => ''];
        if (isset($_REQUEST['type'])) {
            $type = $_REQUEST['type'];
        }
        if (isset($_REQUEST['submit'])) {
            $paramsArray = $_REQUEST['Model'];
            $ok = $this->add($paramsArray);
            if (!$ok) {
                $error = $this->getError();
            }
            if ($ok && $type !== 'ajax') {
                $this->redirect([$this->controllerName . "/list"]);
            }
            if ($type === 'ajax') {
                $ajaxResult['ok'] = $ok;
                if ($ok) {
                    Output::sendInJson($ajaxResult);
                }
            }
        }
        $units = Unit::find()->all();
        if ($type === 'ajax') {
            //return $this->renderPartial("add", ["error" => $error, "units" => $units]);
            $ajaxResult['html'] = $this->renderPartial("add", ["error" => $error, "units" => $units]);
            Output::sendInJson($ajaxResult);
        } else {
            return $this->render("add", ["error" => $error, "units" => $units]);
        }
    }
    
    public function actionChange() {
        $error = "";
        $id = $_REQUEST[$this->primaryKeyName];
        $model = Unit::findOne($id);
        $ajaxResult = ['ok' => true, 'html' => ''];
        $type = (isset($_REQUEST['type']) ? $_REQUEST['type'] : '');
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
        $allUnits = Unit::find()->all();
        $units = [];
        foreach ($allUnits as $unit) {
            if ($unit->unit_id !== $model->unit_id) {
                $units[] = $unit;
            }
        }
        if ($type === 'ajax') {
            $ajaxResult['html'] = $this->renderPartial("change", ["error" => $error, "model" => $model, "units" => $units]);
            Output::sendInJson($ajaxResult);
        } else {
            return $this->render("change", ["error" => $error, "model" => $model, "units" => $units]);
        }
    }
    
    public function actionDelete() {
        $id = $_REQUEST[$this->primaryKeyName];
        if (isset($id)) {
            $model = Unit::findOne($id);
            $code = $model->delete();
            $this->redirect([$this->controllerName . "/list"]);
        }
    }
    
    private function add($paramsArray) {
        $model = new Unit();
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