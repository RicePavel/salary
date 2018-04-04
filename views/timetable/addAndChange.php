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

<form ng-submit="save()">
    <!-- <button type="button" class="btn btn-primary" >Сохранить</button> -->
    
    <br/>
    Дата: <input type="text" class="dateInput" name="Model[create_date]" ng-model="timetableModel.create_date" required /> <br/><br/>
    
    Месяц:
    <select name="Model[month]" required  class="monthSelect" ng-model="timetableModel.month" ng-change="updateDays()" convert-to-number >
        <option ng-repeat="(key, month) in months" value="{{key}}" >{{month}}</option>
    </select>
    
    Год:
    <select name="Model[year]" required  class="yearSelect" ng-model="timetableModel.year" ng-change="updateDays()" convert-to-number >
        <option ng-repeat='year in years' value='{{year}}' >{{year}}</option>
    </select>
    <br/><br/>
    
    Подразделение: 
    <select name="Model[unit_id]" required ng-model="timetableModel.unit_id" convert-to-number >
        <option ng-repeat='unit in units' value='{{unit.unit_id}}' >{{unit.name}}</option>
    </select> <br/> <br/> 
    
    <input type="submit" class="btn btn-primary" value="Сохранить" /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <a class="btn btn-default" href="<?= $listUrl ?>" >Отмена</a>
    <!-- <input type="submit" name="submit" value="Добавить" /> -->
</form>

<br/><br/>

<button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal" >Добавить пользователя</button>

<br/>

<!-- deprecated -->



<div class="tableContainer">
    <div class="secondTableContainer">
        <table class="table table-bordered timetable-table-1">
            <tr>
                <td class="number-column">№</td>
                <td class="worker-column" width="300" >Сотрудник</td>
                <td class="total-column" width="200" >Итого</td>
            </tr>
            <tbody ng-repeat="timetable_worker in getDaysInfoArray()">
                <tr ng-repeat="timetable_row in  timetable_worker.rows">
                    <td>{{$index == 0 ? $parent.$index + 1 : ''}}</td>
                    <td>{{$index == 0 ? timetable_worker.fio : ''}}
                        <button ng-show="$index === 0" ng-click="deleteTimetableWorker($parent.$index)" type="button" class="btn btn-default btn-xs timetableDeleteButton" > <span class="glyphicon glyphicon-remove"></span> </button>
                    </td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        
        <table class="table table-bordered timetable-table-2">
            <tr>
                <td ng-repeat="day in days" ng-class="(day.holiday) ? 'holiday' : '' ">{{day.dayOfMonth}}&nbsp;{{day.dayOfWeekName}}</td>
            </tr>
            <tbody ng-repeat="(timetableWorkerIndex, timetable_worker) in getDaysInfoArray()">
                <tr ng-repeat="(rowIndex, timetable_row) in timetable_worker.rows">
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



