<?php

use yii\helpers\Url;

$controllerName = "unit";
$primaryKeyName = "unit_id";

$addUrl = Url::to([$controllerName . "/add"]);

?>

<div> <?= $error ?> </div>

<form action="<?= $addUrl ?>" method="POST" class="form-horizontal addForm">
    <div class="form-group">
        <label class="col-sm-3 control-label"> Наименование: </label>
        <div class="col-sm-5"> 
            <input class="form-control" type="text" name="Model[name]" value="<?= isset($_REQUEST["Model[name]"]) ? $_REQUEST["Model[name]"] : '' ?>" required />
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label"> Вышестоящее подразделение:  </label>
        <div class="col-sm-5">
            <select class="form-control" name="Model[parent_id]">
                <option value="" >--</option>
                <?php foreach ($units as $unit) { ?>
                    <option value="<?= $unit->unit_id ?>"><?= $unit->name ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
    <input type="hidden" name="<?= Yii::$app->getRequest()->csrfParam ?>" value="<?= Yii::$app->getRequest()->getCsrfToken() ?>" />
    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-5">
            <input type="submit" class="btn btn-primary" name="submit" value="Добавить" />
        </div>
    </div>
</form>
