<?php
     
use yii\helpers\Url;

$controllerName = "employment-type";
$primaryKeyName = "employment_type_id";

$addUrl = Url::to([$controllerName . "/add"]);

?>

<div class="employmentTypeContainer"> 
    <h2>Виды рабочего времени</h2> <br/> <br/>
    <a href="<?= $addUrl ?>" class="addLink">Добавить </a> <br/><br/>

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
            <td> <a href="<?= $changeUrl ?>" class="changeLink" data-id="<?= $elem->employment_type_id ?>">Изменить</a> </td>
            <td> <a onclick="return confirm('Подтвердите удаление')" href="<?= $deleteUrl ?>">Удалить</a> </td>
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

