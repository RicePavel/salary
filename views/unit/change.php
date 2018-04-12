<?php

$controllerName = "unit";
$primaryKeyName = "unit_id";

use yii\helpers\Url;

$changeUrl = Url::to([$controllerName . "/change"]);

?>

<div> <?= $error ?> </div>

<form action="<?= $changeUrl ?>" method="POST" class="form-horizontal changeForm" >
    <div class="form-group">
        <label class="col-sm-2 control-label">Наименование:</label>
        <div class="col-sm-5"> 
            <input class="form-control" type="text" name="Model[name]" value="<?= $model->name ?>" required />
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">Вышестоящее подразделение:</label> 
        <div class="col-sm-5"> 
            <select class="form-control" name="Model[parent_id]">
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
            </select>
        </div>
    </div>
    <input type="hidden" name="<?= Yii::$app->getRequest()->csrfParam ?>" value="<?= Yii::$app->getRequest()->getCsrfToken() ?>" />
    <input type="hidden" name="<?= $primaryKeyName ?>" value="<?= $model->$primaryKeyName ?>" />
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-5">
            <input type="submit" class="btn btn-primary" name="submit" value="Изменить" />
        </div>
    </div>
</form>