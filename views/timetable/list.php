<?php
     
use yii\helpers\Url;

$controllerName = "timetable";
$primaryKeyName = "timetable_id";

$addUrl = Url::to([$controllerName . "/add", 'type' => 'showAddAjaxForm']);

?>

<h2>Табели учета рабочего времени</h2> <br/> <br/>
<a class="btn btn-default my-btn" href="<?= $addUrl ?>" >Добавить </a> <br/><br/>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        new TableResize(document.getElementById('timetableTable'), {restoreState: true, fixed: true});
    });
</script>

<table class="table table-bordered" id="timetableTable">
    <tr class="active">
        <th><a href="<?= Url::to(["timetable/list", 'orderColumn' => 'create_date']) ?>">Дата</a></th>
        <th><a href="<?= Url::to(["timetable/list", 'orderColumn' => 'period']) ?>">Период регистрации</a></th>
        <th><a href="<?= Url::to(["timetable/list", 'orderColumn' => 'unit.name']) ?>" >Подразделение</a></th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
    </tr>
    <?php foreach ($list as $elem) {
        $changeUrl = Url::to([$controllerName . "/change", $primaryKeyName => $elem->$primaryKeyName, 'type' => 'showAjaxForm']);
        $deleteUrl = Url::to([$controllerName . "/delete", $primaryKeyName => $elem->$primaryKeyName]);
        ?>
    <tr>
        <td> <?= $elem->create_date ?> </td>
        <td> <?= $months[$elem->month] ?> <?= $elem->year ?> </td>
        <td> <?= $elem->unit->name ?>  </td>
        <td> <a class="btn btn-default my-btn" href="<?= $changeUrl ?>">Открыть</a> </td>
        <td> 
            <form onsubmit="return confirm('Подтвердите удаление')" action="<?= $deleteUrl ?>" method="POST" >
                <input type="hidden" name="<?= Yii::$app->getRequest()->csrfParam ?>" value="<?= Yii::$app->getRequest()->getCsrfToken() ?>" />
                <input type="submit" value="удалить" class="btn btn-default my-btn" />
            </form>
        </td>
    </tr>
    <?php } ?>
</table>

