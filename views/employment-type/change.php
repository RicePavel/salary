<?php

$controllerName = "employment-type";
$primaryKeyName = "employment_type_id";

use yii\helpers\Url;

$changeUrl = Url::to([$controllerName . "/change"]);

?>

<div> <?= $error ?> </div>

<form action="<?= $changeUrl ?>" method="POST" class="form-horizontal changeForm">
    <div class="form-group">
        <label class="col-sm-3 control-label"> Наименование: </label>
        <div class="col-sm-5"> 
            <input class="form-control" type="text" name="Model[name]" value="<?= $model->name ?>" required />
         </div>
    </div>
    <input type="hidden" name="<?= $primaryKeyName ?>" value="<?= $model->$primaryKeyName ?>" />
    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-5">
            <input type="submit" class="btn btn-primary" name="submit" value="Изменить" />
        </div>
    </div>
</form>