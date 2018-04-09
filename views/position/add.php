<?php

use yii\helpers\Url;

$controllerName = "position";
$primaryKeyName = "position_id";

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
        <div class="col-sm-offset-3 col-sm-5">
            <input class="btn btn-primary" type="submit" name="submit" value="Добавить" />
        </div>
    </div>
</form>
