<?php

use yii\helpers\Url;

$controllerName = "timetable";
$primaryKeyName = "timetable_id";

$changeUrl = Url::to([$controllerName . "/change"]);


?>

<div ng-controller="timetableController">

<div> <?= $error ?> </div>

<form action="<?= $changeUrl ?>" method="POST" >
    Дата: <input type="text" class="dateInput" name="Model[create_date]" value="<?= $model->create_date ?>" required /> <br/><br/>
    
    Месяц:
    <select name="Model[month]" required class="monthSelect">
        <?php foreach ($months as $monthNumber => $month) { ?>
        <option value="<?= $monthNumber ?>"
                
                <?php if ($monthNumber === $model->month) { echo 'selected'; } ?>
                
                ><?= $month ?></option>
        <?php } ?>
    </select>
    
    Год:
    <select name="Model[year]" required class="yearSelect">
        <?php foreach ($years as $year) { ?>
        <option value="<?= $year ?>"
                
                <?php if ($year === $model->year) { echo 'selected'; } ?>
                
                ><?= $year ?></option>
        <?php } ?>
    </select>
    <br/><br/>
    
    Подразделение: 
    <select name="Model[unit_id]" required >
        <?php foreach ($units as $unit) { ?>
            <option value="<?= $unit->unit_id ?>" 
                    
                    <?php 
                    
                    if ($unit->unit_id === $model->unit_id) {
                        echo 'selected';
                    }
                    ?>
                    
                    > <?= $unit->name ?> </option>
        <?php } ?>
    </select> <br/> <br/>
    <input type="hidden" name="timetable_id" value="<?= $model->timetable_id ?>" />
    <input type="submit" name="submit" value="Сохранить" />
</form>

<br/> <br/>

<table class="table table-bordered">
    <tr>
        <td ng-repeat="day in days" ng-class="(day.holiday) ? 'holiday' : '' ">{{day.dayOfMonth}}&nbsp;{{day.dayOfWeekName}}</td>
    </tr>
</table>

</div>