<?php
use backend\assets\AppAsset;
use yii\helpers\Html;
use dmstr\widgets\Alert;

/* @var $this \yii\web\View */
/* @var $content string */

frontend\assets\AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="login-page">

<?php $this->beginBody() ?>

<div class="login-box">

<?= Alert::widget() ?>

    <div class="login-logo">
        <a href="<?php echo Yii::$app->homeUrl; ?>"><b><?=Yii::$app->params['appName'];?></b> - 0.1</a>
        
    </div>
    <!-- /.login-logo -->

    <?= $content ?>

 </div><!-- /.login-box -->

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
