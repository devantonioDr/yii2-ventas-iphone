<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\telefono\TelefonoMarcaModelo */
/* @var $marcas array */

$this->title = 'Añadir Nueva Marca y Modelo';
$this->params['breadcrumbs'][] = ['label' => 'Marcas y Modelos', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Añadir';
?>

<div class="telefono-marca-modelo-create">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-plus"></i> Añadir Nueva Marca y Modelo
                    </h3>
                    <div class="box-tools pull-right">
                        <?= Html::a('<i class="fa fa-arrow-left"></i> Volver', ['index'], [
                            'class' => 'btn btn-default btn-sm'
                        ]) ?>
                    </div>
                </div>
                <div class="box-body">
                    <?= $this->render('_form', [
                        'model' => $model,
                        'marcas' => $marcas,
                        'modelos' => [],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
