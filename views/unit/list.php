<?php
     
use yii\helpers\Url;

$controllerName = "unit";
$primaryKeyName = "unit_id";

$addUrl = Url::to([$controllerName . "/add"]);

?>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        new TableResize(document.getElementById('unitTable'), {restoreState: true, fixed: true});
    });
</script>

<div class="unitContainer">
    <h2>Подразделения</h2> <br/> <br/>
    <a href="<?= $addUrl ?>" class="addLink btn btn-default my-btn" >Добавить </a> <br/><br/>

    <table class="table table-bordered" id="unitTable">
        <tr class="active">
            <th><a href="<?= Url::to(["unit/list", 'orderColumn' => 'name']) ?>">Наименование</a></th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
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
                <td> <a href="<?= $changeUrl ?>" class="changeLink btn btn-default my-btn" data-id="<?= $unit->unit_id ?>" >Изменить</a> </td>
                <td> 
                    <form onsubmit="return confirm('Подтвердите удаление')" action="<?= $deleteUrl ?>" method="POST" >
                        <input type="hidden" name="<?= Yii::$app->getRequest()->csrfParam ?>" value="<?= Yii::$app->getRequest()->getCsrfToken() ?>" />
                        <input type="submit" value="Удалить" class="btn btn-default my-btn" />
                    </form>
                </td>
            </tr>
            <?= renderUnits($unit->getChildren(), ($level + 1), $controllerName, $primaryKeyName) ?> 
        <?php } ?>
    <?php } ?>

    <?php function renderTest() { ?>
        <div>1111</div>
    <?php } ?>


    <!-- модальное окно -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel"></h4>
          </div>
          <div class="modal-body">
            ...
          </div>
          <div class="modal-footer">

          </div>
        </div>
      </div>
    </div>

</div>