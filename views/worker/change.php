<?php

$controllerName = "worker";
$primaryKeyName = "worker_id";

use yii\helpers\Url;
use app\models\Work_type;

$changeUrl = Url::to([$controllerName . "/change"]);

?>

<div> <?= $error ?> </div>

<form action="<?= $changeUrl ?>" method="POST" >
    ФИО: <input type="text" name="Model[fio]" value="<?= $model->fio ?>" required /> <br/>
    Табельный номер: <input type="text" name="Model[person_number]" value="<?= $model->person_number ?>" required /> <br/> 
    Должность:
    <select name="Model[position_id]">
        <?php 
        foreach ($positions as $position) {
            
            $id = $position->position_id;
            $name = $position->name;
            
            ?>
            <option value="<?= $position->position_id ?>" <?php if ($model->position_id === $position->position_id ) { echo 'selected';} ?> > <?= $position->name ?> </option>
        <?php } ?>
    </select> <br/>
    <br/>
    Подразделение:
    <select name="Model[unit_id]">
        <option value="">--</option>
        <?php foreach ($units as $unit) { ?>
            <option <?php if ($unit->unit_id === $model->unit_id) { echo 'selected'; } ?> value="<?= $unit->unit_id ?>"> <?= $unit->name ?> </option>
        <?php } ?>
    </select> <br/><br/>
    Вид занятости:
    <select name="Model[work_type_id]">
        <option value="">--</option>
        <?php foreach(Work_type::getList() as $work_type_id) {?>
            <option <?php if ($work_type_id === $model->work_type_id) { echo 'selected'; } ?> value="<?= $work_type_id ?>"><?= Work_type::getName($work_type_id) ?></option>
        <?php } ?>
    </select><br/><br/>
    <input type="hidden" name="<?= $primaryKeyName ?>" value="<?= $model->$primaryKeyName ?>" />
    <input type="submit" name="submit" value="Изменить" />
</form>