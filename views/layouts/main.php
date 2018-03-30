<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    
     <script type="text/javascript" src="http://code.jquery.com/jquery-2.1.3.min.js"></script> 
     <script src="bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
    
    <link rel="stylesheet" type="text/css" href="bootstrap-3.3.7-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/my.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    
    <script src="js/my.js"></script>
    
    <script src="js/angular.min.js"></script>    
    
    <script src="js/angular_app/main.js" ></script>
    <script src="js/angular_app/controllers/timetable.js"></script>
    
    <script>
  $( function() {
    $( "#datepicker" ).datepicker();
  } );
  </script>
    
    <?php  $this->head()  ?>
</head>
<body>
<?php $this->beginBody() ?>

    
    
<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
            ['label' => 'Табели', 'url' => ['timetable/list']],
            ['label' => 'Сотрудники', 'url' => ['worker/list']],
            ['label' => 'Должности', 'url' => ['position/list']],
            ['label' => 'Подразделения', 'url' => ['unit/list']],
            ['label' => 'Виды занятости', 'url' => ['employment-type/list']]
        ],
        /*
        'items' => [
            ['label' => 'Home', 'url' => ['/site/index']],
            ['label' => 'About', 'url' => ['/site/about']],
            ['label' => 'Contact', 'url' => ['/site/contact']],
            Yii::$app->user->isGuest ? (
                ['label' => 'Login', 'url' => ['/site/login']]
            ) : (
                '<li>'
                . Html::beginForm(['/site/logout'], 'post')
                . Html::submitButton(
                    'Logout (' . Yii::$app->user->identity->username . ')',
                    ['class' => 'btn btn-link logout']
                )
                . Html::endForm()
                . '</li>'
            )
        ],
        */
    ]);
    NavBar::end();
    ?>

    <div class="container" ng-app="myApp" >
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        
        <?= $content ?>
    </div>
</div>

<!-- 
<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>
-->

 <?php /* $this->endBody() */ ?> 
</body>
</html>
<?php $this->endPage() ?>
