<?php

use yii\helpers\Url;
use app\models\Work_type;

$controllerName = "worker";
$primaryKeyName = "worker_id";

$addUrl = Url::to([$controllerName . "/add"]);

?>

<div> <?= $error ?> </div>

<form action="<?= $addUrl ?>" method="POST" >
    ФИО: <input type="text" name="Model[fio]" value="<?= isset($_REQUEST["Model[name]"]) ? $_REQUEST["Model[name]"] : '' ?>" required /> <br/> 
    Табельный номер: <input type="text" name="Model[person_number]" value="<?= isset($_REQUEST["Model[person_number]"]) ? $_REQUEST["Model[person_number]"] : '' ?>" required /> <br/> 
    Должность: 
    <select name="Model[position_id]" required >
        <?php foreach ($positions as $position) { ?>
            <option value="<?= $position->position_id ?>"> <?= $position->name ?> </option>
        <?php } ?>
    </select> <br/> <br/>
    Подразделение:
    <select name="Model[unit_id]">
        <option value="">--</option>
        <?php foreach ($units as $unit) { ?>
            <option value="<?= $unit->unit_id ?>"> <?= $unit->name ?> </option>
        <?php } ?>
    </select> <br/><br/>
    Вид занятости:
    <select name="Model[work_type_id]">
        <option value="">--</option>
        <?php foreach(Work_type::getList() as $work_type_id) {?>
            <option value="<?= $work_type_id ?>"><?= Work_type::getName($work_type_id) ?></option>
        <?php } ?>
    </select><br/><br/>
    <input type="submit" name="submit" value="Добавить" />
</form>
