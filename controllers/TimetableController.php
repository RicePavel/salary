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
use app\models\Day_info;
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
            // просто показать форму
            return $this->render("addAndChange");
        } else if ($type == 'addByAjax') {
            // добавить по ajax запросу
            $ok = true;
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $paramsArray = $_REQUEST['Model'];
                $timetableWorkerArray = [];
                if (isset($_REQUEST['timetableWorkerArray'])) {
                    $timetableWorkerArray = BaseJson::decode($_REQUEST['timetableWorkerArray']);
                    if (!is_array($timetableWorkerArray)) {
                        $timetableWorkerArray = [];
                    }
                }
                $daysInfoArray = [];
                if (isset($_REQUEST['daysInfoArray'])) {
                    $daysInfoArray = BaseJson::decode($_REQUEST['daysInfoArray']);
                    if (!is_array($daysInfoArray)) {
                        $daysInfoArray = [];
                    }
                }
                $ok = $this->add($paramsArray, $timetableWorkerArray, $daysInfoArray);
                
            } catch (Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
            if ($ok) {
                $transaction->commit();
            } else {
                $transaction->rollBack();
            }
            if (!$ok) {
                    $error = $this->getError();
            }
            $result = ['ok' => $ok, 'error' => $error];
            $this->sendInJson($result);
        } else {
            // добавить по обычному get-запросу
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
            $daysInfoArray = [];
            if ($model != null) {
                $timetableWorkerArray = $this->getTimetableWorkerArray($model->timetable_id);
                $daysInfoArray = $this->getDaysInfoArray($model->timetable_id);
            }
            $result = ['model' => $model, 
                "years" => $this->getYears(),
                "months" => $this->getMonths(),
                "units" => $this->getUnits(),
                'ok' => $ok,
                'error' => $error,
                'timetableWorkerArray' => $timetableWorkerArray,
                'employmentTypes' => $this->getEmploymentTypes(),
                'daysInfoArray' => $daysInfoArray];
            $this->sendInJson($result);
        } else if ($type == 'byAjax') {
            // изменить по ajax-запросу
            $transaction = Yii::$app->db->beginTransaction();
            $ok = true;
            $error = '';
            try {
                $paramsArray = $_REQUEST['Model'];
                $id = $paramsArray[$this->primaryKeyName];
                $model = Timetable::findOne($id);
                $ok = $this->change($model, $paramsArray);
                if ($ok) {
                    if (isset($_REQUEST['timetableWorkerArray'])) {
                        $timetableWorkerArray = BaseJson::decode($_REQUEST['timetableWorkerArray']);
                        $daysInfoArray = [];
                        if (isset($_REQUEST['daysInfoArray'])) {
                            $daysInfoArray = BaseJson::decode($_REQUEST['daysInfoArray']);
                            if (!is_array($daysInfoArray)) {
                                $daysInfoArray = [];
                            }
                        }
                        if (is_array($timetableWorkerArray)) {
                            $this->updateTimetableInformation($id, $timetableWorkerArray, $daysInfoArray);
                        }
                    }
                }
            } catch (Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
            if ($ok) {
                $transaction->commit();
            } else {
                $transaction->rollBack();
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
    
    private function getDaysInfoArray($timetableId) {
        $daysInfoArray = [];
        $timetableModel = Timetable::find()->where(['timetable_id' => $timetableId])->with('timetable_workers.days_info.employment_type')->one();
        foreach ($timetableModel->timetable_workers as $timetable_worker) {
            $number = $timetable_worker->number;
            $arrayRowNumber = $number - 1;
            $daysInfoArray[$arrayRowNumber] = [
                'timetable_worker_id' => $timetable_worker->timetable_worker_id,
                'days' => []
            ];
            foreach ($timetable_worker->days_info as $element) {
                $day = $element->day;
                $daysInfoArray[$arrayRowNumber]['days'][$day] = [
                    'time' => $element->time,
                    'employment_type_id' => $element->employment_type_id,
                    'employment_type_short_name' => $element->employment_type->short_name
                ];
            }
        }
        return $daysInfoArray;
    }
    
    private function updateTimetableInformation($timetableId, $timetableWorkerArray, $daysInfoArray) {
        $idsArray = [];
        // удаление записей, которые надо удалить
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
        // сохранение новых записей
        $number = 1;
        foreach ($timetableWorkerArray as $arrayRowNumber => $elem) {
            $timetableWorkerId = '';
            if (($elem['timetable_worker_id']) && $elem['timetable_worker_id'] != '') {
                $timetableWorkerId = $elem['timetable_worker_id'];
                $model = Timetable_worker::findOne($timetableWorkerId);
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
                $timetableWorkerId = $model->timetable_worker_id;
                if (!$ok) {
                    $this->setErrorFromModel($model);
                    return false;
                }
            }
            if ($ok) {
                $ok = $this->saveDaysInfoRow($daysInfoArray, $arrayRowNumber, $timetableWorkerId);
                if (!$ok) {
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
    
    private function add($paramsArray, $timetableWorkerArray, $daysInfoArray) {
        $model = new Timetable();
        $model->setAttributes($paramsArray, false);
        $this->beforeSave($model);
        $ok = $model->save();
        if ($ok) {
            $timetable_id = $model->timetable_id;
            $number = 1;
            foreach ($timetableWorkerArray as $key => $element) {
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
                $timetableWorkerId = $timetableWorkerModel->timetable_worker_id;
                $ok = $this->saveDaysInfoRow($daysInfoArray, $key, $timetableWorkerId);
                if (!$ok) {
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
    
    
    private function saveDaysInfoRow($daysInfoArray, $rowNumber, $timetableWorkerId) {
        $this->deleteDaysInfoRow($timetableWorkerId);
        $row = $daysInfoArray[$rowNumber];
        if (isset($row)) {
            $days = $row['days'];
            if (isset($days)) {
                foreach ($days as $dayNumber => $elem) {
                    $time = isset($elem['time']) ? $elem['time'] : null;
                    if (isset($elem['employment_type_id'])) {
                        $employment_type_id = $elem['employment_type_id'];
                        $model = new Day_info();
                        $model->day = $dayNumber;
                        $model->time = $time;
                        $model->timetable_worker_id = $timetableWorkerId;
                        $model->employment_type_id = $employment_type_id;
                        $ok = $model->save();
                        if (!$ok) {
                            $this->setErrorFromModel($model);
                            return false;
                        }
                    }
                }
            }
        }
        return true;
    }
    
    private function deleteDaysInfoRow($timetableWorkerId) {
        $conn = Yii::$app->db;
        $command = $conn->createCommand('delete from day_info where timetable_worker_id = :timetable_worker_id ', ['timetable_worker_id' => $timetableWorkerId]);
        $command->execute();
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