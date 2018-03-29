<?php

$controllerName = "position";
$primaryKeyName = "position_id";

use yii\helpers\Url;

$changeUrl = Url::to([$controllerName . "/change"]);

?>

<div> <?= $error ?> </div>

<form action="<?= $changeUrl ?>" method="POST" >
    Наименование: <input type="text" name="Model[name]" value="<?= $model->name ?>" required /> <br/> <br/>
    <input type="hidden" name="<?= $primaryKeyName ?>" value="<?= $model->$primaryKeyName ?>" />
    <input type="submit" name="submit" value="Изменить" />
</form>