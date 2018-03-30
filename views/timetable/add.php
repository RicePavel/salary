<?php

use yii\helpers\Url;

$controllerName = "timetable";
$primaryKeyName = "timetable_id";

$addUrl = Url::to([$controllerName . "/add"]);

$createDate = (isset($_REQUEST["Model[create_date]"]) ? $_REQUEST["Model[create_date]"] : $currentDate);

?>

<div ng-controller="timetableController">

<div> <?= $error ?> </div>

<!-- форма для обычной работы, не через ajax.  deprecated.   -->
<?php if (isset($months, $years, $units)) { ?>
<form action="<?= $addUrl ?>" method="POST" style="display: none;" >
    
    Дата: <input type="text" class="dateInput" name="Model[create_date]" value="<?= $createDate?>" required /> <br/><br/>
    
    Месяц:
    <select name="Model[month]" required  class="monthSelect" >
        <?php foreach ($months as $monthNumber => $month) { ?>
        <option value="<?= $monthNumber ?>"
                
                <?php if ($monthNumber === $currentMonth) { echo 'selected'; } ?>
                
                ><?= $month ?></option>
        <?php } ?>
    </select>
    
    Год:
    <select name="Model[year]" required  class="yearSelect" >
        <?php foreach ($years as $year) { ?>
        <option value="<?= $year ?>"
                
                <?php if ($year === $currentYear) { echo 'selected'; } ?>
                
                ><?= $year ?></option>
        <?php } ?>
    </select>
    <br/><br/>
    
    Подразделение: 
    <select name="Model[unit_id]" required >
        <?php foreach ($units as $unit) { ?>
            <option value="<?= $unit->unit_id ?>"> <?= $unit->name ?> </option>
        <?php } ?>
    </select> <br/> <br/>
    <input type="submit" name="submit" value="Добавить" />
</form>
<?php } ?>
<!-- end form -->
<br/> 


<form>
    <button type="button" class="btn btn-primary">Сохранить</button>
    <br/>
    <br/>
    Дата: <input type="text" class="dateInput" name="Model[create_date]" ng-model="timetableModel.create_date" required /> <br/><br/>
    
    Месяц:
    <select name="Model[month]" required  class="monthSelect" ng-model="timetableModel.month" >
        <option ng-repeat="month in months" value="{{month.number}}" >{{month.name}}</option>
    </select>
    
    Год:
    <select name="Model[year]" required  class="yearSelect" ng-model="timetableModel.year" >
        <option ng-repeat='year in years' value='{{year}}' >{{year}}</option>
    </select>
    <br/><br/>
    
    Подразделение: 
    <select name="Model[unit_id]" required ng-model="timetableModel.unit_id" >
        <option ng-repeat='unit in inits' value='{{unit.unit_id}}' >{{unit.name}}</option>
    </select> <br/> <br/>
    <!-- <input type="submit" name="submit" value="Добавить" /> -->
</form>

<button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal" >Добавить пользователя</button>

<br/>

<div class="tableContainer">
    <table class="table table-bordered timetable-table-1">
        <tr>
            <td class="worker-column" width="300" >Сотрудник</td>
            <td class="total-column" width="200" >Итого</td>
        </tr>
        <tr ng-repeat="timetableWorker in  timetableWorkerArray">
            <td>{{timetableWorker.worker.fio}}
                <button ng-click="deleteTimetableWorker($index)" type="button" class="btn btn-default btn-xs timetableDeleteButton" > <span class="glyphicon glyphicon-remove"></span> </button>
            </td>
            <td></td>
        </tr>
    </table>
    <table class="table table-bordered timetable-table-2">
        <tr>
            <td ng-repeat="day in days" ng-class="(day.holiday) ? 'holiday' : '' ">{{day.dayOfMonth}}&nbsp;{{day.dayOfWeekName}}</td>
        </tr>
        <tr ng-repeat="timetableWorker in timetableWorkerArray">
            <td ng-repeat="day in days"></td>
        </tr>
    </table>
</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" >
        <div class="modal-content" >
            <div class="modal-header">Добавление пользователя</div>
            <div class="modal-body">
                
                <form ng-submit="addWorker()" >
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



