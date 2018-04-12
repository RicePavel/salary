<?php

use yii\helpers\Url;
use app\models\Work_type;

$controllerName = "worker";
$primaryKeyName = "worker_id";

$addUrl = Url::to([$controllerName . "/add"]);

?>

<div> <?= $error ?> </div>

<form action="<?= $addUrl ?>" method="POST" class="form-horizontal addForm" >
    <div class="form-group">
        <label class="col-sm-3 control-label"> ФИО: </label> 
        <div class="col-sm-5">
            <input class="form-control" type="text" name="Model[fio]" value="<?= isset($_REQUEST["Model[name]"]) ? $_REQUEST["Model[name]"] : '' ?>" required /> 
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label"> Табельный номер: </label>
        <div class="col-sm-5">
            <input class="form-control" type="text" name="Model[person_number]" value="<?= isset($_REQUEST["Model[person_number]"]) ? $_REQUEST["Model[person_number]"] : '' ?>" required /> 
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label"> Должность: </label> 
        <div class="col-sm-5">
            <select class="form-control" name="Model[position_id]" required >
                <?php foreach ($positions as $position) { ?>
                    <option value="<?= $position->position_id ?>"> <?= $position->name ?> </option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label"> Подразделение: </label>
        <div class="col-sm-5">
            <select class="form-control" name="Model[unit_id]">
                <option value="">--</option>
                <?php foreach ($units as $unit) { ?>
                    <option value="<?= $unit->unit_id ?>"> <?= $unit->name ?> </option>
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
                    <option value="<?= $work_type_id ?>"><?= Work_type::getName($work_type_id) ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
    <input type="hidden" name="<?= Yii::$app->getRequest()->csrfParam ?>" value="<?= Yii::$app->getRequest()->getCsrfToken() ?>" />
    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-5">
            <input class="btn btn-primary" type="submit" name="submit" value="Добавить" />
        </div>
    </div>
</form>
