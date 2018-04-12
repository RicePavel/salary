<?php

$controllerName = "position";
$primaryKeyName = "position_id";

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
    <input type="hidden" name="<?= Yii::$app->getRequest()->csrfParam ?>" value="<?= Yii::$app->getRequest()->getCsrfToken() ?>" />
    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-5">
            <input class="btn btn-primary" type="submit" name="submit" value="Изменить" />
        </div>
    </div>
</form>