<?php
     
use yii\helpers\Url;
use app\models\Work_type;

$controllerName = "worker";
$primaryKeyName = "worker_id";

$addUrl = Url::to([$controllerName . "/add"]);

?>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        new TableResize(document.getElementById('workerTable'), {restoreState: true, fixed: true});
    });
</script>

<div class="workerContainer">
    <h2>Сотрудники</h2> <br/> <br/>
    <a class="btn btn-default my-btn" href="<?= $addUrl ?>" class="addLink" >Добавить </a> <br/><br/>

    <table class="table table-bordered" id="workerTable">
        <tr class="active">
            <th><a href="<?= Url::to(["worker/list", 'orderColumn' => 'fio']) ?>">ФИО</a></th>
            <th><a href="<?= Url::to(["worker/list", 'orderColumn' => 'person_number']) ?>">Табельный номер</a></th>
            <th><a href="<?= Url::to(["worker/list", 'orderColumn' => 'position.name']) ?>">Должность</a></th>
            <th><a href="<?= Url::to(["worker/list", 'orderColumn' => 'unit.name']) ?>">Подразделение</a></th>
            <th><a href="<?= Url::to(["worker/list", 'orderColumn' => 'code_1c']) ?>">Код из 1С</a></th>
            <th>Вид занятости</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
        </tr>
        <?php foreach ($list as $elem) {
            $changeUrl = Url::to([$controllerName . "/change", $primaryKeyName => $elem->$primaryKeyName]);
            $deleteUrl = Url::to([$controllerName . "/delete", $primaryKeyName => $elem->$primaryKeyName]);
            ?>
        <tr>
            <td><?= $elem->fio ?></td>
            <td><?= $elem->person_number ?></td>
            <td> <?= ($elem->position !== null ? $elem->position->name : '') ?> </td>
            <td> <?= ($elem->unit !== null ? $elem->unit->name : '') ?> </td>
            <td> <?= $elem->code_1c ?> </td>
            <td> <?= Work_type::getName($elem->work_type_id) ?> </td>
            <td> 
                <a href="<?= $changeUrl ?>" class="btn btn-default my-btn changeLink" data-id="<?= $elem->worker_id ?>" >Изменить</a> 
            </td>
            <!-- <td> <a onclick="return confirm('Подтвердите удаление')" href="<?= $deleteUrl ?>">Удалить</a> </td> -->
            <td>
                <form onsubmit="return confirm('Подтвердите удаление')" action="<?= $deleteUrl ?>" method="POST" >
                    <input type="hidden" name="<?= Yii::$app->getRequest()->csrfParam ?>" value="<?= Yii::$app->getRequest()->getCsrfToken() ?>" />
                    <input type="submit" value="Удалить" class="btn btn-default my-btn" />
                </form>
            </td>
        </tr>
        <?php } ?>
    </table>
    
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

