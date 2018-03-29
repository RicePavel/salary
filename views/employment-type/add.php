<?php

use yii\helpers\Url;

$controllerName = "employment-type";
$primaryKeyName = "employment_type_id";

$addUrl = Url::to([$controllerName . "/add"]);

?>

<div> <?= $error ?> </div> <br/>

<form action="<?= $addUrl ?>" method="POST" >
    Сокращение: <input type="text" name="Model[short_name]" value="<?= $_REQUEST["Model"]["short_name"] ?>" required /> <br/>
    Наименование: <input type="text" name="Model[name]" value="<?= $_REQUEST["Model"]["name"] ?>" required /> <br/>  
    <input type="submit" name="submit" value="Добавить" />
</form>
