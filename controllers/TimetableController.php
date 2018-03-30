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
use app\models\Timetable;
use app\helpers\DateFormat;
use yii\helpers\BaseJson;

class TimetableController extends Controller
{
    
    public $enableCsrfValidation = false;
    
    private $error = "";
    
    private $controllerName = "timetable";
    
    private $primaryKeyName = "timetable_id";
    
    private function setError($errorArray) {
        $this->error = implode(", ", $errorArray);
    }
    
    private function getError() {
        return $this->error;
    }
    
    private function getList() {
       return Timetable::find()->with('unit')->all(); 
    }
    
    private function getMonths() {
        return [
            1 => 'Январь',
            2 => 'Февраль',
            3 => 'Март',
            4 => 'Апрель',
            5 => 'Май',
            6 => 'Июнь',
            7 => 'Июль',
            8 => 'Август',
            9 => 'Сентябрь',
            10 => 'Октябрь',
            11 => 'Ноябрь',
            12 => 'Декабрь'
        ]; 
    }
    
    private function getCurrentMonth() {
        return getdate()["mon"];        
    }
    
    private function getYears() {
        $currentYear = $this->getCurrentYear();
        $start = $currentYear - 10;
        $end = $currentYear + 10;
        $years = [];
        for ($i = $start; $i <= $end; $i++) {
            $years[] = $i; 
        }
        return $years;
    }
    
    private function getCurrentDateInWebFormat() {
        return date('d.m.Y');
    }
    
    private function getCurrentYear() {
        return getdate()["year"];
    }
    
    private function sendInJson($data) {
        $json = BaseJson::encode($data);
        $res = Yii::$app->getResponse();
        $res->format = Response::FORMAT_JSON;
        $res->data = $json;
        $res->send();
    }
    
    public function actionList() {
        $list = $this->getList(); 
        foreach ($list as $model) {
            $this->beforeOutput($model);
        }
        return $this->render("list", ["list" => $list, "units" => $this->getUnits(), "months" => $this->getMonths()]);
    }
    
    private function getUnits() {
        return Unit::find()->all();
    }
    
    public function actionAdd() {
        $error = "";
        $type = '';
        if (isset($_REQUEST['type'])) {
            $type = $_REQUEST['type'];
        }
        if ($type == 'showAddAjaxForm') {
            return $this->render("add");
        } else {
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
        return $this->render("add", ["error" => $error, "years" => $this->getYears(), "currentYear" => $this->getCurrentYear(),
            "months" => $this->getMonths(), "currentMonth" => $this->getCurrentMonth(), "units" => $this->getUnits(),
            "currentDate" => $this->getCurrentDateInWebFormat()]);
        }
    }
    
    public function actionAdd_info() {
        $error = "";
        $result = ["years" => $this->getYears(), "currentYear" => $this->getCurrentYear(),
            "months" => $this->getMonths(), "currentMonth" => $this->getCurrentMonth(), "units" => $this->getUnits(),
            "currentDate" => $this->getCurrentDateInWebFormat()];
        $this->sendInJson($result);
    }
    
    public function actionChange() {
        $error = "";
        $id = $_REQUEST[$this->primaryKeyName];
        $model = Timetable::findOne($id);
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
        $this->beforeOutput($model);
        return $this->render("change", ["error" => $error, "model" => $model, "years" => $this->getYears(), "months" => $this->getMonths(),
               "units" => $this->getUnits()]);
    }
    
    public function actionDelete() {
        $id = $_REQUEST[$this->primaryKeyName];
        if (isset($id)) {
            $model = Timetable::findOne($id);
            $code = $model->delete();
            $this->redirect([$this->controllerName . "/list"]);
        }
    }
    
    private function add($paramsArray) {
        $model = new Timetable();
        $model->setAttributes($paramsArray, false);
        $this->beforeSave($model);
        $ok = $model->save();
        if (!$ok) {
            $this->setError($model->getErrorSummary(true));
        }
        return $ok;
    }
    
    private function change($model, $paramsArray) {
        $model->setAttributes($paramsArray, false);
        $this->beforeSave($model);
        $ok = $model->save();
        if (!$ok) {
            $this->setError($model->getErrorSummary(true));
        }
        return $ok;
    }
    
    private function beforeSave($model) {
       if ($model->create_date) {
           $model->create_date = DateFormat::toSqlFormat($model->create_date);
       } 
    }
    
    private function beforeOutput($model) {
        if ($model->create_date) {
            $model->create_date = DateFormat::toWebFormat($model->create_date);
        }
    }

}