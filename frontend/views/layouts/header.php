<?php

use common\components\MyHelpers;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */
?>

<header class="main-header">

    <?= Html::a('<span class="logo-mini">CRM</span><span class="logo-lg">' . Yii::$app->params['appName']. '</span>', Yii::$app->homeUrl, ['class' => 'logo']) ?>

    <nav class="navbar navbar-static-top" role="navigation">

        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
      
        <h4 class="alcaldeTitle"><?=""?></h4>
      
        <div class="navbar-custom-menu">

            <ul class="nav navbar-nav">
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="<?= Yii::getAlias('@web').'/images/nophoto.jpg';?>" class="user-image" alt="User Image"/>
                        <span class="hidden-xs"><?=Yii::$app->user->identity->username; ?> </span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">

                            <img src="<?= Yii::getAlias('@web').'/images/nophoto.jpg';?>" class="img-circle"
                                 alt="User Image"/>

                            <p>
                                <?= Yii::$app->user->identity->username; ?>

                                <small><?=date('d/m/Y',Yii::$app->user->identity->created_at); ?> </small>
                            </p>
                        </li>
                        <!-- Menu Body -->
                        <li class="user-body">
                            <div class="col-xs-12 text-center">
                                <?= Html::a(
                                    'Archivos Generados',
                                    ['/files-system-reports/index'],
                                    ['class' => 'btn btn-default btn-flat']
                                ) ?>
                            </div>


                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <?= Html::a(
                                    'Perfil',
                                    ['/user/update', 'id'=>Yii::$app->user->identity->id],
                                    ['class' => 'btn btn-default btn-flat']
                                ) ?>


                            </div>
                            <div class="pull-right">
                                <?= Html::a(
                                    'Sign out',
                                    ['/site/logout'],
                                    ['data-method' => 'post', 'class' => 'btn btn-default btn-flat']
                                ) ?>
                            </div>
                        </li>
                    </ul>
                </li>

            </ul>
        </div>
    </nav>


</header>
<style>
  .skin-blue .main-header .navbar,
  .skin-blue .main-header li.user-header {
    background-color: #222d32 !important;
  }

  .logo {
    background-color: #222d32 !important;
  }
  .sidebar-toggle{
    background-color: #222d32 !important;
  }
</style>