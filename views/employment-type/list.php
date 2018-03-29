<?php
     
use yii\helpers\Url;

$controllerName = "employment-type";
$primaryKeyName = "employment_type_id";

$addUrl = Url::to([$controllerName . "/add"]);

?>

<h2>Виды занятости</h2> <br/> <br/>
<a href="<?= $addUrl ?>" >Добавить </a> <br/><br/>

<table class="table">
    <tr>
        <th>Сокращение</th>
        <th>Наименование</th>
        <th></th>
        <th></th>
    </tr>
    <?php foreach ($list as $elem) {
        $changeUrl = Url::to([$controllerName . "/change", $primaryKeyName => $elem->$primaryKeyName]);
        $deleteUrl = Url::to([$controllerName . "/delete", $primaryKeyName => $elem->$primaryKeyName]);
        ?>
    <tr>
        <td><?= $elem->short_name ?></td>
        <td><?= $elem->name ?></td>
        <td> <a href="<?= $changeUrl ?>">Изменить</a> </td>
        <td> <a onclick="return confirm('Подтвердите удаление')" href="<?= $deleteUrl ?>">Удалить</a> </td>
    </tr>
    <?php } ?>
</table>

