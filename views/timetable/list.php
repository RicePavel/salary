<?php
     
use yii\helpers\Url;

$controllerName = "timetable";
$primaryKeyName = "timetable_id";

$addUrl = Url::to([$controllerName . "/add", 'type' => 'showAddAjaxForm']);

?>

<h2>Табели</h2> <br/> <br/>
<a href="<?= $addUrl ?>" >Добавить </a> <br/><br/>

<table class="table">
    <tr>
        <th>Дата</th>
        <th>Период регистрации</th>
        <th>Подразделение</th>
        <th></th>
        <th></th>
    </tr>
    <?php foreach ($list as $elem) {
        $changeUrl = Url::to([$controllerName . "/change", $primaryKeyName => $elem->$primaryKeyName, 'type' => 'showAjaxForm']);
        $deleteUrl = Url::to([$controllerName . "/delete", $primaryKeyName => $elem->$primaryKeyName]);
        ?>
    <tr>
        <td> <?= $elem->create_date ?> </td>
        <td> <?= $months[$elem->month] ?> <?= $elem->year ?> </td>
        <td> <?= $elem->unit->name ?>  </td>
        <td> <a href="<?= $changeUrl ?>"><button class="btn btn-link">Открыть</button></a> </td>
        <td> 
            <form onsubmit="return confirm('Подтвердите удаление')" action="<?= $deleteUrl ?>" method="POST" >
                <input type="hidden" name="<?= Yii::$app->getRequest()->csrfParam ?>" value="<?= Yii::$app->getRequest()->getCsrfToken() ?>" />
                <input type="submit" value="удалить" class="btn btn-link" />
            </form>
        </td>
    </tr>
    <?php } ?>
</table>

