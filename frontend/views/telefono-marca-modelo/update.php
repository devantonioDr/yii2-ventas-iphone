<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\telefono\TelefonoMarcaModelo */
/* @var $marcas array */
/* @var $modelos array */

$this->title = 'Editar: ' . $model->marca . ' ' . $model->modelo;
$this->params['breadcrumbs'][] = ['label' => 'Marcas y Modelos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->marca . ' ' . $model->modelo, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Editar';
?>

<div class="telefono-marca-modelo-update">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-pencil"></i> Editar Marca y Modelo
                    </h3>
                    <div class="box-tools pull-right">
                        <?= Html::a('<i class="fa fa-eye"></i> Ver', ['view', 'id' => $model->id], [
                            'class' => 'btn btn-info btn-sm'
                        ]) ?>
                        <?= Html::a('<i class="fa fa-arrow-left"></i> Volver', ['index'], [
                            'class' => 'btn btn-default btn-sm'
                        ]) ?>
                    </div>
                </div>
                <div class="box-body">
                    <?= $this->render('_form', [
                        'model' => $model,
                        'marcas' => $marcas,
                        'modelos' => $modelos,
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
