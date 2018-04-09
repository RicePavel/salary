<?php
     
use yii\helpers\Url;

$controllerName = "unit";
$primaryKeyName = "unit_id";

$addUrl = Url::to([$controllerName . "/add"]);

?>

<h2>Подразделения</h2> <br/> <br/>
<a href="<?= $addUrl ?>" >Добавить </a> <br/><br/>

<table class="table">
    <tr>
        <th>Наименование</th>
        <th></th>
        <th></th>
    </tr>
    <?= renderUnits($list, 0, $controllerName, $primaryKeyName) ?>
</table>

<?php function renderUnits($units, $level, $controllerName, $primaryKeyName) { ?>
    <?php foreach ($units as $unit) {
        $changeUrl = Url::to([$controllerName . "/change", $primaryKeyName => $unit->$primaryKeyName]);
        $deleteUrl = Url::to([$controllerName . "/delete", $primaryKeyName => $unit->$primaryKeyName]);
    ?>
        <tr>
            <td>
                <?php 
                    for ($i = 0; $i < $level; $i++) {
                        echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                    }
                ?>
                <?= $unit->name ?>
            </td>
            <td> <a href="<?= $changeUrl ?>">Изменить</a> </td>
            <td> <a onclick="return confirm('Подтвердите удаление')" href="<?= $deleteUrl ?>">Удалить</a> </td>
        </tr>
        <?= renderUnits($unit->getChildren(), ($level + 1), $controllerName, $primaryKeyName) ?> 
    <?php } ?>
<?php } ?>

<?php function renderTest() { ?>
    <div>1111</div>
<?php } ?>

