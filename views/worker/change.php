<?php

$controllerName = "worker";
$primaryKeyName = "worker_id";

use yii\helpers\Url;

$changeUrl = Url::to([$controllerName . "/change"]);

?>

<div> <?= $error ?> </div>

<form action="<?= $changeUrl ?>" method="POST" >
    ФИО: <input type="text" name="Model[fio]" value="<?= $model->fio ?>" required /> <br/>
    Табельный номер: <input type="text" name="Model[person_number]" value="<?= $model->person_number ?>" required /> <br/> 
    Должность:
    <select name="Model[position_id]">
        <?php 
        foreach ($positions as $position) {
            
            $id = $position->position_id;
            $name = $position->name;
            
            ?>
            <option value="<?= $position->position_id ?>" <?php if ($model->position_id === $position->position_id ) { echo 'selected';} ?> > <?= $position->name ?> </option>
        <?php } ?>
    </select> <br/>
    <br/>
    <input type="hidden" name="<?= $primaryKeyName ?>" value="<?= $model->$primaryKeyName ?>" />
    <input type="submit" name="submit" value="Изменить" />
</form>