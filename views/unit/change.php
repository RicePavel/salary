<?php

$controllerName = "unit";
$primaryKeyName = "unit_id";

use yii\helpers\Url;

$changeUrl = Url::to([$controllerName . "/change"]);

?>

<div> <?= $error ?> </div>

<form action="<?= $changeUrl ?>" method="POST" >
    Наименование: <input type="text" name="Model[name]" value="<?= $model->name ?>" required /> <br/> <br/>
    Вышестоящее подразделение: 
    <select name="Model[parent_id]">
        <option value="" >--</option>
        <?php foreach ($units as $unit) { ?>
            <option  
                <?php
                if ($unit->unit_id === $model->parent_id) {
                    echo 'selected';
                } 
                ?> 
                value="<?= $unit->unit_id ?>">
                        <?= $unit->name ?>
            </option>
        <?php } ?>
    </select> <br/><br/>
    <input type="hidden" name="<?= $primaryKeyName ?>" value="<?= $model->$primaryKeyName ?>" />
    <input type="submit" name="submit" value="Изменить" />
</form>