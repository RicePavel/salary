<?php

$controllerName = "worker";
$primaryKeyName = "worker_id";

use yii\helpers\Url;
use app\models\Work_type;

$changeUrl = Url::to([$controllerName . "/change"]);

?>

<div> <?= $error ?> </div>

<form action="<?= $changeUrl ?>" method="POST" class="form-horizontal changeForm" >
    <div class="form-group">
        <label class="col-sm-3 control-label">ФИО: </label> 
        <div class="col-sm-5">
            <input class="form-control" type="text" name="Model[fio]" value="<?= $model->fio ?>" required /> 
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label">Табельный номер: </label> 
        <div class="col-sm-5">
            <input class="form-control" type="text" name="Model[person_number]" value="<?= $model->person_number ?>" required /> 
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label">Должность: </label> 
        <div class="col-sm-5">
            <select class="form-control" name="Model[position_id]">
                <?php 
                foreach ($positions as $position) {

                    $id = $position->position_id;
                    $name = $position->name;

                    ?>
                    <option value="<?= $position->position_id ?>" <?php if ($model->position_id === $position->position_id ) { echo 'selected';} ?> > <?= $position->name ?> </option>
                <?php } ?>
            </select> 
         </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label">Подразделение: </label> 
        <div class="col-sm-5">
            <select class="form-control" name="Model[unit_id]">
                <option value="">--</option>
                <?php foreach ($units as $unit) { ?>
                    <option <?php if ($unit->unit_id === $model->unit_id) { echo 'selected'; } ?> value="<?= $unit->unit_id ?>"> <?= $unit->name ?> </option>
                <?php } ?>
            </select> 
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label"> Вид занятости: </label> 
        <div class="col-sm-5">
            <select class="form-control" name="Model[work_type_id]">
                <option value="">--</option>
                <?php foreach(Work_type::getList() as $work_type_id) {?>
                    <option <?php if ($work_type_id === $model->work_type_id) { echo 'selected'; } ?> value="<?= $work_type_id ?>"><?= Work_type::getName($work_type_id) ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
    <input type="hidden" name="<?= $primaryKeyName ?>" value="<?= $model->$primaryKeyName ?>" />
    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-5">
            <input class="btn btn-primary" type="submit" name="submit" value="Изменить" />
        </div>
    </div>
</form>