<?php
     
use yii\helpers\Url;

$controllerName = "employment-type";
$primaryKeyName = "employment_type_id";

$addUrl = Url::to([$controllerName . "/add"]);

?>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        new TableResize(document.getElementById('employmentTypeTable'), {restoreState: true, fixed: true});
    });
</script>

<div class="employmentTypeContainer"> 
    <h2>Виды рабочего времени</h2> <br/> <br/>
    <a href="<?= $addUrl ?>" class="addLink btn btn-default my-btn">Добавить </a> <br/><br/>

    <table class="table table-bordered" id="employmentTypeTable">
        <tr class="active">
            <th><a href="<?= Url::to(["employment-type/list", 'orderColumn' => 'short_name']) ?>">Сокращение</a></th>
            <th><a href="<?= Url::to(["employment-type/list", 'orderColumn' => 'name']) ?>">Наименование</a></th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
        </tr>
        <?php foreach ($list as $elem) {
            $changeUrl = Url::to([$controllerName . "/change", $primaryKeyName => $elem->$primaryKeyName]);
            $deleteUrl = Url::to([$controllerName . "/delete", $primaryKeyName => $elem->$primaryKeyName]);
            ?>
        <tr>
            <td><?= $elem->short_name ?></td>
            <td><?= $elem->name ?></td>
            <td> <a href="<?= $changeUrl ?>" class="changeLink btn btn-default my-btn" data-id="<?= $elem->employment_type_id ?>">Изменить</a> </td>
            <td> 
                <form onsubmit="return confirm('Подтвердите удаление')" action="<?= $deleteUrl ?>" method="POST" >
                    <input type="hidden" name="<?= Yii::$app->getRequest()->csrfParam ?>" value="<?= Yii::$app->getRequest()->getCsrfToken() ?>" />
                    <input type="submit" value="удалить" class="btn btn-default my-btn" />
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

