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
use app\models\Timetable_worker;
use app\models\Employment_type;
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
    
    private function setErrorFromModel($model) {
        $this->setError($model->getErrorSummary(true));
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
            return $this->render("addAndChange");
        } else if ($type == 'addByAjax') {
            $paramsArray = $_REQUEST['Model'];
            $timetableWorkerArray = [];
            if (isset($_REQUEST['timetableWorkerArray'])) {
                $timetableWorkerArray = BaseJson::decode($_REQUEST['timetableWorkerArray']);
                if (!is_array($timetableWorkerArray)) {
                    $timetableWorkerArray = [];
                }
            }
            $ok = $this->add($paramsArray, $timetableWorkerArray);
            if (!$ok) {
                $error = $this->getError();
            }
            $result = ['ok' => $ok, 'error' => $error];
            $this->sendInJson($result);
        } else {
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
            return $this->render("add", ["error" => $error, "years" => $this->getYears(), "currentYear" => $this->getCurrentYear(),
                "months" => $this->getMonths(), "currentMonth" => $this->getCurrentMonth(), "units" => $this->getUnits(),
                "currentDate" => $this->getCurrentDateInWebFormat()]);
        }
    }
    
    public function actionAdd_info() {
        $error = "";
        $result = ["years" => $this->getYears(), "currentYear" => $this->getCurrentYear(),
            "months" => $this->getMonths(), "currentMonth" => $this->getCurrentMonth(), "units" => $this->getUnits(),
            "currentDate" => $this->getCurrentDateInWebFormat(), 'employmentTypes' => $this->getEmploymentTypes()];
        $this->sendInJson($result);
    }
    
    public function actionChange() {
        $type = '';
        if (isset($_REQUEST['type'])) {
            $type = $_REQUEST['type'];
        }
        if ($type == 'showAjaxForm') {
            // просто вывести форму
            return $this->render('addAndChange');
        } else if ($type == 'getDataForChange') {
            // получить данные для изменения
            $id = $_REQUEST[$this->primaryKeyName];
            $model = Timetable::findOne($id);
            $this->beforeOutput($model);
            $ok = true;
            $error = '';
            if ($model == null) {
                $ok = false;
                $error = 'ошибка: данные не найдены';
            }
            $timetableWorkerArray = [];
            if ($model != null) {
                $timetableWorkerArray = $this->getTimetableWorkerArray($model->timetable_id);
            }
            $result = ['model' => $model, 
                "years" => $this->getYears(),
                "months" => $this->getMonths(),
                "units" => $this->getUnits(),
                'ok' => $ok,
                'error' => $error,
                'timetableWorkerArray' => $timetableWorkerArray,
                'employmentTypes' => $this->getEmploymentTypes()];
            $this->sendInJson($result);
        } else if ($type == 'byAjax') {
            // изменить по ajax-запросу
            $error = '';
            $paramsArray = $_REQUEST['Model'];
            $id = $paramsArray[$this->primaryKeyName];
            $model = Timetable::findOne($id);
            $ok = $this->change($model, $paramsArray);
            if ($ok) {
                if (isset($_REQUEST['timetableWorkerArray'])) {
                    $timetableWorkerArray = BaseJson::decode($_REQUEST['timetableWorkerArray']);
                    if (is_array($timetableWorkerArray)) {
                        $this->updateTimetableWorkerArray($id, $timetableWorkerArray);
                    }
                }
            }
            if (!$ok) {
                $error = $this->getError();
            }
            $result = ['ok' => $ok, 'error' => $error];
            $this->sendInJson($result);
        } else {
            // изменить по обычному GET-запросу
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
    }
    
    private function updateTimetableWorkerArray($timetableId, $timetableWorkerArray) {
        $idsArray = [];
        foreach ($timetableWorkerArray as $elem) {
           if (($elem['timetable_worker_id']) && $elem['timetable_worker_id'] != '') {
               $idsArray[] = (string) trim($elem['timetable_worker_id']);
           }  
        }
        $modelsFromBase = Timetable_worker::findAll(['timetable_id' => $timetableId]);
        foreach ($modelsFromBase as $modelFromBase) {
            $idFromBase = (string) trim($modelFromBase->timetable_worker_id);
            if (!in_array($idFromBase, $idsArray)) {
                $i = $modelFromBase->delete();
                if ($i === false) {
                    $this->setErrorFromModel($model);
                    return false;
                }
            }
        }
        
        $number = 1;
        foreach ($timetableWorkerArray as $elem) {
            if (($elem['timetable_worker_id']) && $elem['timetable_worker_id'] != '') {
                $model = Timetable_worker::findOne($elem['timetable_worker_id']);
                if ($model != null) {
                    $model->worker_id = $elem['worker_id'];
                    $model->number = $number;
                    $ok = $model->save();
                    if (!$ok) {
                        $this->setErrorFromModel($model);
                        return false;
                    }
                }
            } else {
                $model = new Timetable_worker();
                $model->timetable_id = $timetableId;
                $model->worker_id = $elem['worker_id'];
                $model->number = $number;
                $ok = $model->save();
                if (!$ok) {
                    $this->setErrorFromModel($model);
                    return false;
                }
            }
            $number++;
        }
        return true;
    }
    
    public function actionDelete() {
        $id = $_REQUEST[$this->primaryKeyName];
        if (isset($id)) {
            $model = Timetable::findOne($id);
            $code = $model->delete();
            $this->redirect([$this->controllerName . "/list"]);
        }
    }
    
    private function add($paramsArray, $timetableWorkerArray) {
        $model = new Timetable();
        $model->setAttributes($paramsArray, false);
        $this->beforeSave($model);
        $ok = $model->save();
        if ($ok) {
            $timetable_id = $model->timetable_id;
            $number = 1;
            foreach ($timetableWorkerArray as $element) {
                $worker_id = $element['worker_id'];
                $timetableWorkerModel = new Timetable_worker();
                $timetableWorkerModel->timetable_id = $timetable_id;
                $timetableWorkerModel->worker_id = $worker_id;
                $timetableWorkerModel->number = $number;
                $ok = $timetableWorkerModel->save();
                if (!$ok) {
                    $this->setError($model->getErrorSummary(true));
                    return false;
                }
                $number++;
            }
        }
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
    
    private function getTimetableWorkerArray($timetableId) {
        $query = new \yii\db\Query();
        $query->select('tw.timetable_worker_id, tw.timetable_id, tw.worker_id, tw.number, w.fio as fio')
                ->from('timetable_worker as tw')
                ->innerJoin('worker as w', 'tw.worker_id = w.worker_id')
                ->where('tw.timetable_id = :timetable_id', ['timetable_id' => $timetableId])
                ->orderBy('tw.number');
        return $query->all();
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
    
    private function getEmploymentTypes() {
        return Employment_type::find()->all();
    }

}