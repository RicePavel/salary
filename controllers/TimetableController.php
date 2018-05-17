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
use app\models\Timetable_row;
use app\models\Employment_type;
use app\helpers\DateFormat;
use yii\helpers\BaseJson;

class TimetableController extends Controller
{
    
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
    
    private function getList($orderColumn = '', $orderType = 'ASC') {
       $sql = "SELECT * FROM timetable left join unit on timetable.unit_id = unit.unit_id "; 
       
       if ($orderColumn) {
           if ($orderColumn === 'period') {
               $orderColumn = 'year, month';
           }
           $sql .= ' order by ' . $orderColumn . ' ' . $orderType;
       }
        
       return Timetable::findBySql($sql)->with('unit')->all(); 
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
        $isGuest = Yii::$app->user->isGuest;   
        $userId = Yii::$app->user->id;
        
        $orderColumn = Yii::$app->request->get("orderColumn");
        if ($orderColumn) {
            $oldOrderType = Yii::$app->session->get("timetable_orderType");
            $oldOrderColumn = Yii::$app->session->get("timetable_orderColumn");
            $orderType = 'ASC';
            if ($oldOrderType === 'ASC' && $oldOrderColumn === $orderColumn) {
                $orderType = 'DESC';
            }
            $list = $this->getList($orderColumn, $orderType);
            Yii::$app->session->set("timetable_orderType", $orderType);
            Yii::$app->session->set("timetable_orderColumn", $orderColumn);
        } else {
            $list = $this->getList();
            Yii::$app->session->remove("timetable_orderType");
            Yii::$app->session->remove("timetable_orderColumn");
        }
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
                $daysInfoArray = [];
                if (isset($_REQUEST['daysInfoArray'])) {
                    $daysInfoArray = BaseJson::decode($_REQUEST['daysInfoArray']);
                    if (!is_array($daysInfoArray)) {
                        $daysInfoArray = [];
                    }
                }
                $ok = $this->add($paramsArray, $daysInfoArray);
                
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
                $timetableId = $paramsArray[$this->primaryKeyName];
                $model = Timetable::findOne($timetableId);
                $ok = $this->change($model, $paramsArray);
                if ($ok) {
                    $daysInfoArray = [];
                    if (isset($_REQUEST['daysInfoArray'])) {
                        $daysInfoArray = BaseJson::decode($_REQUEST['daysInfoArray']);
                        if (is_array($daysInfoArray)) {
                            $this->saveDaysInfoArray($timetableId, $daysInfoArray);
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
        
        $timetableModel = Timetable::find()->
                where('timetable_id = :timetable_id', ['timetable_id' => $timetableId])
                ->with('timetable_workers.timetable_rows.days_info.employment_type')
                ->with('timetable_workers.worker')->one();
        $daysInfoArray = [];
        
        foreach ($timetableModel->timetable_workers as $timetableWorkerModel) {
            $timetableWorker = ['worker_id' => $timetableWorkerModel->worker_id, 
                'fio' => ($timetableWorkerModel->worker ? $timetableWorkerModel->worker->fio : ''),
                'rows' => []];
            foreach ($timetableWorkerModel->timetable_rows as $rowModel) {
                $row = ['days' => []];
                foreach ($rowModel->days_info as $dayInfoModel) {
                    $day = ['time' => $dayInfoModel->time, 
                        'employment_type_id' => $dayInfoModel->employment_type_id,
                        'short_name' => $dayInfoModel->employment_type->short_name];
                    $row['days'][$dayInfoModel->day] = $day;
                }
                $timetableWorker['rows'][] = $row;
            }
            $daysInfoArray[] = $timetableWorker;
        }
        
        return $daysInfoArray;
    }
    
    public function actionDelete() {
        $id = $_REQUEST[$this->primaryKeyName];
        if (isset($id)) {
            $model = Timetable::findOne($id);
            $code = $model->delete();
            $this->redirect([$this->controllerName . "/list"]);
        }
    }
    
    private function add($paramsArray, $daysInfoArray) {
        $model = new Timetable();
        $model->setAttributes($paramsArray, false);
        $this->beforeSave($model);
        $ok = $model->save();
        if ($ok) {
            $timetable_id = $model->timetable_id;
            
            $this->saveDaysInfoArray($timetable_id, $daysInfoArray);
        }
        if (!$ok) {
            $this->setError($model->getErrorSummary(true));
        }
        return $ok;
    }
    
    private function saveDaysInfoArray($timetableId, $daysInfoArray) {            
            $sql = 'delete from timetable_worker where timetable_id = :timetable_id';
            $command = Yii::$app->db->createCommand($sql, ['timetable_id' => $timetableId]);
            $command->execute();
            
            foreach ($daysInfoArray as $timetableWorkerIndex => $timetableWorker) {
                $timetableWorkerModel = new Timetable_worker();
                $timetableWorkerModel->timetable_id = $timetableId;
                $timetableWorkerModel->worker_id = $timetableWorker['worker_id'];
                $timetableWorkerModel->number = $timetableWorkerIndex;
                $ok = $timetableWorkerModel->save();
                if (!$ok) {
                    $this->setErrorFromModel($timetableWorkerModel);
                    return false;
                }
                $timetableWorkerId = $timetableWorkerModel->timetable_worker_id;
                foreach ($timetableWorker['rows'] as $rowIndex => $row) {
                    $timetableRowModel = new Timetable_row();
                    $timetableRowModel->timetable_worker_id = $timetableWorkerId;
                    $timetableRowModel->number = $rowIndex;
                    $ok = $timetableRowModel->save();
                    if (!$ok) {
                        $this->setErrorFromModel($timetableRowModel);
                        return false;
                    }
                    $timetableRowId = $timetableRowModel->timetable_row_id;
                    foreach ($row['days'] as $dayNumber => $dayInfo) {
                        if (isset($dayInfo)) {
                            $time = isset($dayInfo['time']) ? $dayInfo['time'] : null;
                            $employmentTypeId = isset($dayInfo['employment_type_id']) ? $dayInfo['employment_type_id'] : null;
                            if ($time === null && $employmentTypeId === null) {
                                continue;
                            }
                            $dayInfoModel = new Day_info();
                            $dayInfoModel->day = $dayNumber;
                            $dayInfoModel->time = $time;
                            $dayInfoModel->employment_type_id = $employmentTypeId;
                            $dayInfoModel->timetable_row_id = $timetableRowId;
                            $ok = $dayInfoModel->save();
                            if (!$ok) {
                                $this->setErrorFromModel($dayInfoModel);
                                return false;
                            }
                        }
                    }
                }
            }
            return true;  
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
    
    private function getEmploymentTypes() {
        return Employment_type::find()->all();
    }

}