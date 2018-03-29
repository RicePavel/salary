<?php

use yii\helpers\Url;

$controllerName = "timetable";
$primaryKeyName = "timetable_id";

$addUrl = Url::to([$controllerName . "/add"]);



?>

<div> <?= $error ?> </div>

<form action="<?= $addUrl ?>" method="POST" >
    Дата: <input type="text" name="Model[create_date]" value="<?= $_REQUEST["Model[create_date]"] ?>" required /> <br/><br/>
    Номер: <input type="text" name="Model[number]" value="<?= $_REQUEST["Model[number]"] ?>" required /> <br/><br/>
    
    Месяц:
    <select name="Model[month]" required>
        <?php foreach ($months as $monthNumber => $month) { ?>
        <option value="<?= $monthNumber ?>"
                
                <?php if ($monthNumber === $currentMonth) { echo 'selected'; } ?>
                
                ><?= $month ?></option>
        <?php } ?>
    </select>
    
    Год:
    <select name="Model[year]" required>
        <?php foreach ($years as $year) { ?>
        <option value="<?= $yearNumber ?>"
                
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
