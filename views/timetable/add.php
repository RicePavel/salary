<?php

use yii\helpers\Url;

$controllerName = "timetable";
$primaryKeyName = "timetable_id";

$addUrl = Url::to([$controllerName . "/add"]);

$createDate = (isset($_REQUEST["Model[create_date]"]) ? $_REQUEST["Model[create_date]"] : $currentDate);

?>

<div ng-controller="timetableController">

<div> <?= $error ?> </div>

<form action="<?= $addUrl ?>" method="POST" >
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

<br/> <br/>

<table class="table table-bordered">
    <tr>
        <td ng-repeat="day in days" ng-class="(day.holiday) ? 'holiday' : '' ">{{day.dayOfMonth}}&nbsp;{{day.dayOfWeekName}}</td>
    </tr>
</table>

</div>