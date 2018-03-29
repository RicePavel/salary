<?php
     
use yii\helpers\Url;

$controllerName = "worker";
$primaryKeyName = "worker_id";

$addUrl = Url::to([$controllerName . "/add"]);

?>

<h2>Сотрудники</h2> <br/> <br/>
<a href="<?= $addUrl ?>" >Добавить </a> <br/><br/>

<table class="table">
    <tr>
        <th>ФИО</th>
        <th>Табельный номер</th>
        <th>Должность</th>
        <th></th>
        <th></th>
    </tr>
    <?php foreach ($list as $elem) {
        $changeUrl = Url::to([$controllerName . "/change", $primaryKeyName => $elem->$primaryKeyName]);
        $deleteUrl = Url::to([$controllerName . "/delete", $primaryKeyName => $elem->$primaryKeyName]);
        ?>
    <tr>
        <td><?= $elem->fio ?></td>
        <td><?= $elem->person_number ?></td>
        <td> <?= $elem->position->name ?> </td>
        <td> <a href="<?= $changeUrl ?>">Изменить</a> </td>
        <td> <a onclick="return confirm('Подтвердите удаление')" href="<?= $deleteUrl ?>">Удалить</a> </td>
    </tr>
    <?php } ?>
</table>

