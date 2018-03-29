<?php

use yii\helpers\Url;

$controllerName = "worker";
$primaryKeyName = "worker_id";

$addUrl = Url::to([$controllerName . "/add"]);

?>

<div> <?= $error ?> </div>

<form action="<?= $addUrl ?>" method="POST" >
    ФИО: <input type="text" name="Model[fio]" value="<?= $_REQUEST["Model[name]"] ?>" required /> <br/> 
    Табельный номер: <input type="text" name="Model[person_number]" value="<?= $_REQUEST["Model[person_number]"] ?>" required /> <br/> 
    Должность: 
    <select name="Model[position_id]" required >
        <?php foreach ($positions as $position) { ?>
            <option value="<?= $position->position_id ?>"> <?= $position->name ?> </option>
        <?php } ?>
    </select> <br/>
    <input type="submit" name="submit" value="Добавить" />
</form>
