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

class UnitController extends Controller
{
    
    public $enableCsrfValidation = false;
    
    private $error = "";
    
    private $controllerName = "unit";
    
    private $primaryKeyName = "unit_id";
    
    private function setError($errorArray) {
        $this->error = implode(", ", $errorArray);
    }
    
    private function getError() {
        return $this->error;
    }
    
    private function getList() {
       $units = Unit::find()->all();
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
        $list = $this->getList(); 
        return $this->render("list", ["list" => $list]);
    }
    
    public function actionAdd() {
        $error = "";
        if (isset($_REQUEST['submit'])) {
            $paramsArray = $_REQUEST['Model'];
            $ok = $this->add($paramsArray);
            if (!$ok) {
                $error = $this->getError();
            }
            if ($ok) {
                $this->redirect([$this->controllerName . "/list"]);
            }
        }
        $units = Unit::find()->all();
        return $this->render("add", ["error" => $error, "units" => $units]);
    }
    
    public function actionChange() {
        $error = "";
        $id = $_REQUEST[$this->primaryKeyName];
        $model = Unit::findOne($id);
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
        $allUnits = Unit::find()->all();
        $units = [];
        foreach ($allUnits as $unit) {
            if ($unit->unit_id !== $model->unit_id) {
                $units[] = $unit;
            }
        }
        return $this->render("change", ["error" => $error, "model" => $model, "units" => $units]);
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