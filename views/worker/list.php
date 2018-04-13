<?php
     
use yii\helpers\Url;
use app\models\Work_type;

$controllerName = "worker";
$primaryKeyName = "worker_id";

$addUrl = Url::to([$controllerName . "/add"]);

?>

<div class="workerContainer">
    <h2>Сотрудники</h2> <br/> <br/>
    <a href="<?= $addUrl ?>" class="addLink" >Добавить </a> <br/><br/>

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
            <td> <?= ($elem->position !== null ? $elem->position->name : '') ?> </td>
            <td> <?= ($elem->unit !== null ? $elem->unit->name : '') ?> </td>
            <td> <?= $elem->code_1c ?> </td>
            <td> <?= Work_type::getName($elem->work_type_id) ?> </td>
            <td> 
                <a href="<?= $changeUrl ?>" class="changeLink" data-id="<?= $elem->worker_id ?>" ><button class="btn btn-link">Изменить</button></a> 
            </td>
            <!-- <td> <a onclick="return confirm('Подтвердите удаление')" href="<?= $deleteUrl ?>">Удалить</a> </td> -->
            <td>
                <form onsubmit="return confirm('Подтвердите удаление')" action="<?= $deleteUrl ?>" method="POST" >
                    <input type="hidden" name="<?= Yii::$app->getRequest()->csrfParam ?>" value="<?= Yii::$app->getRequest()->getCsrfToken() ?>" />
                    <input type="submit" value="удалить" class="btn btn-link" />
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

