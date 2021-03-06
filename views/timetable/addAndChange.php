<?php

use yii\helpers\Url;

$controllerName = "timetable";
$primaryKeyName = "timetable_id";

$addUrl = Url::to([$controllerName . "/add"]);
$listUrl = Url::to([$controllerName .  "/list"]);

//$createDate = (isset($_REQUEST["Model[create_date]"]) ? $_REQUEST["Model[create_date]"] : $currentDate);

?>

<div ng-controller="timetableController">


<br/> 

<style type="text/css">
    .container {
        width: 100% !important;
    }
    .tableContainer {
        width: 100% !important;
    }
</style>

<form ng-submit="save($event)" id="timetableForm" >
    <!-- <button type="button" class="btn btn-primary" >Сохранить</button> -->
    
    
    Месяц:
    <select name="Model[month]" required  class="monthSelect" ng-model="timetableModel.month" ng-change="updateDays()" convert-to-number >
        <option ng-repeat="(key, month) in months" value="{{key}}" >{{month}}</option>
    </select>
    
    Год:
    <select name="Model[year]" required  class="yearSelect" ng-model="timetableModel.year" ng-change="updateDays()" convert-to-number >
        <option ng-repeat='year in years' value='{{year}}' >{{year}}</option>
    </select>
    
    &nbsp;&nbsp;
    
    Дата: <input type="text" class="dateInput" name="Model[create_date]" ng-model="timetableModel.create_date" required />
    <br/><br/>
    
    Подразделение: 
    <select name="Model[unit_id]" class="unitsSelect" required ng-model="timetableModel.unit_id" convert-to-number >
        <option ng-repeat='unit in units' value='{{unit.unit_id}}' >{{unit.name}}</option>
    </select> 
    <div style="display: none;">
        Максимальное количество видов времени на одну дату: <input class="countRowsInput" type="number" ng-change="changeCountRows()" ng-model="timetableModel.count_rows_on_day" />
    </div>
    
    <input type="hidden" class="csrfInput" name="<?= Yii::$app->getRequest()->csrfParam ?>" value="<?= Yii::$app->getRequest()->getCsrfToken() ?>" />
    
</form>

<br/>

<!-- кнопка добавления пользователя с появлением всплывающего окна. Сейчас не используется. -->
<button style="display: none;" type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal" ><span class="glyphicon glyphicon-plus"></span> &nbsp; Добавить пользователя</button>

<button type="button" class="btn btn-default" ng-click="addEmptyTimetableWorker()" ><span class="glyphicon glyphicon-plus"></span> &nbsp; Добавить пользователя</button>

<br/>


<div class="tableContainer">
    <div class="secondTableContainer">
        <table class="table table-bordered timetable-table-1">
            <tr class="active">
                <td class="number-column">№</td>
                <td class="worker-column" width="300" >Сотрудник</td>
                <td class="total-column" width="200" >Итого</td>
            </tr>
            <tbody ng-repeat="(timetableWorkerIndex, timetable_worker) in getDaysInfoArray()">
                <tr ng-repeat="(rowIndex, timetable_row) in  timetable_worker.rows" ng-class="timetableWorkerIndex % 2 !== 0 ? 'darkRow' : ''">
                    <!-- номер -->
                    <td>{{$index == 0 ? $parent.$index + 1 : ''}}</td>
                    <!-- сотрудник -->
                    <td class="workerTd" >
                        
                        <span class="workerFio">{{$index == 0 ? timetable_worker.fio : ''}}</span>
                        <select style="display: none;" class="workerFioSelect" ng-if="rowIndex === 0" ng-change="changeWorker(timetableWorkerIndex, getDaysInfoArray()[timetableWorkerIndex].worker_id)" ng-model="getDaysInfoArray()[timetableWorkerIndex].worker_id" convert-to-number >
                            <option ng-repeat="worker in workers" value="{{worker.worker_id}}" >{{worker.fio}}</option>
                        </select>
                        
                        <div class="timetableWorkerSelectContainer"> 
                            <input type="text" class="timetableWorkerInput" />
                            <ul class="workersList">
                                <li class="workersListLi" ng-repeat="worker in workers" ng-click="changeWorkerAndCloseSelect(timetableWorkerIndex, worker.worker_id, $event)" data-id="{{worker.worker_id}}" >{{worker.fio}}</li>
                            </ul>
                        </div>
                        
                        <div class="dropdown dropDownContainer" ng-if="rowIndex === 0" class="dropdown">
                            <button id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="btn btn-default btn-xs" >
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dLabel" >
                                <li><a ng-click="copyTimetableWorker(timetableWorkerIndex)" ><span class="glyphicon glyphicon-plus"></span>&nbsp;Скопировать</a></li>
                                <li><a ng-click="deleteTimetableWorker(timetableWorkerIndex)" ><span style="color: red;" class="glyphicon glyphicon-remove"></span>&nbsp;Удалить</a></li>
                                <li><a ng-click="up(timetableWorkerIndex)" ><span class="glyphicon glyphicon-arrow-up"></span>&nbsp;Переместить вверх</a></li>
                                <li><a ng-click="down(timetableWorkerIndex)" ><span class="glyphicon glyphicon-arrow-down"></span>&nbsp;Переместить вниз</a></li>
                            </ul>
                        </div>
                    </td>
                    <!-- итого -->
                    <td class="active"><div ng-if="rowIndex === 0" title="{{getTotalText(timetableWorkerIndex)}}">{{getPartTotalText(timetableWorkerIndex)}}</div></td>
                </tr>
            </tbody>
        </table>
        
        <table class="table table-bordered timetable-table-2">
            <tr class="active">
                <td ng-repeat="day in days" ng-class="(day.holiday) ? 'holiday' : '' ">{{day.dayOfMonth}}&nbsp;{{day.dayOfWeekName}}</td>
            </tr>
            <tbody ng-repeat="(timetableWorkerIndex, timetable_worker) in getDaysInfoArray()">
                <tr ng-repeat="(rowIndex, timetable_row) in timetable_worker.rows" ng-class="timetableWorkerIndex % 2 !== 0 ? 'darkRow' : ''" >
                    <td class="dayCell" ng-repeat="day in days" ng-dblclick="editCell(timetableWorkerIndex, rowIndex, day.dayOfMonth, $event)" >
                        <div class="dayCellDiv" ng-hide="cellEdited(timetableWorkerIndex, rowIndex, day.dayOfMonth)" >
                            {{getDayElementText(timetableWorkerIndex, rowIndex, day.dayOfMonth)}}
                        </div>
                        <input class="dayCellInput" type="text"  ng-model="editableModel.text" ng-show="cellEdited(timetableWorkerIndex, rowIndex, day.dayOfMonth)" />
                        <ul class="employmentTypesList" ng-show="showEmploymentTypesList(timetableWorkerIndex, rowIndex, day.dayOfMonth)" >
                            <li class="notFound" ng-show="editableModel.notFoundEmploymentType" >{{editableModel.notFoundText}} не найдено</li>
                            <li ng-repeat="type in employmentTypes" ng-click="selectEmploymentType(type.short_name, $event)" ng-show="type.show"  >{{type.name}} ({{type.short_name}})</li>
                        </ul>
                    </td>
                </tr>
            </tbody>
        </table>
        
    </div>
</div>

<div class="timetable-button-container">
    <input type="submit" class="btn btn-primary" value="Сохранить табель" form="timetableForm" /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <a class="btn btn-default" href="<?= $listUrl ?>" form="timetableForm" >Закрыть</a>
</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" >
        <div class="modal-content" >
            <div class="modal-header">Добавление пользователя</div>
            <div class="modal-body">
                
                <form ng-submit="addTimetableWorker()" >
                    <select ng-model="workerIdForAdding">
                        <option ng-repeat="worker in workers" value="{{worker.worker_id}}">{{worker.fio}}</option>
                    </select> <br/><br/>
                    <input type="submit" value="Добавить" />
                </form>
                
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>

</div>



