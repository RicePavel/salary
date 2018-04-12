<?php

use yii\helpers\Url;

$controllerName = "employment-type";
$primaryKeyName = "employment_type_id";

$addUrl = Url::to([$controllerName . "/add"]);

?>

<div> <?= $error ?> </div> <br/>

<form action="<?= $addUrl ?>" method="POST" class="form-horizontal addForm" >
    <div class="form-group">
        <label class="col-sm-3 control-label"> Сокращение: </label>
        <div class="col-sm-5"> 
            <input class="form-control" type="text" name="Model[short_name]" value="<?= isset($_REQUEST["Model"]["short_name"]) ? $_REQUEST["Model"]["short_name"] : '' ?>" required />
        </div>    
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label"> Наименование: </label>
        <div class="col-sm-5"> 
            <input class="form-control" type="text" name="Model[name]" value="<?= isset($_REQUEST["Model"]["name"]) ? $_REQUEST["Model"]["name"] : '' ?>" required />  
        </div>
    </div>
    <input type="hidden" name="<?= Yii::$app->getRequest()->csrfParam ?>" value="<?= Yii::$app->getRequest()->getCsrfToken() ?>" />
    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-5">
            <input type="submit" class="btn btn-primary" name="submit" value="Добавить" />
        </div>
    </div>
</form>
