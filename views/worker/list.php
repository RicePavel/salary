<?php
     
use yii\helpers\Url;
use app\models\Work_type;

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
        <th>Подразделение</th>
        <th>Код из 1С</th>
        <th>Вид занятости</th>
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
        <td> <?= $elem->unit->name ?> </td>
        <td> <?= $elem->code_1c ?> </td>
        <td> <?= Work_type::getName($elem->work_type_id) ?> </td>
        <td> <a href="<?= $changeUrl ?>">Изменить</a> </td>
        <td> <a onclick="return confirm('Подтвердите удаление')" href="<?= $deleteUrl ?>">Удалить</a> </td>
    </tr>
    <?php } ?>
</table>

