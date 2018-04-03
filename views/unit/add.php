<?php

use yii\helpers\Url;

$controllerName = "unit";
$primaryKeyName = "unit_id";

$addUrl = Url::to([$controllerName . "/add"]);

?>

<div> <?= $error ?> </div>

<form action="<?= $addUrl ?>" method="POST" >
    Наименование: <input type="text" name="Model[name]" value="<?= isset($_REQUEST["Model[name]"]) ? $_REQUEST["Model[name]"] : '' ?>" required /> <br/> <br/>
    <input type="submit" name="submit" value="Добавить" />
</form>
