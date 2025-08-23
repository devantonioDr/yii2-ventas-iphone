<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\telefono\TelefonoMarcaModelo */
/* @var $telefonosCount int */

$this->title = $model->marca . ' ' . $model->modelo;
$this->params['breadcrumbs'][] = ['label' => 'Marcas y Modelos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="telefono-marca-modelo-view">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-info-circle"></i> Detalles de Marca y Modelo
                    </h3>
                    <div class="box-tools pull-right">
                        <?= Html::a('<i class="fa fa-pencil"></i> Editar', ['update', 'id' => $model->id], [
                            'class' => 'btn btn-primary btn-sm'
                        ]) ?>
                        <?= Html::a('<i class="fa fa-trash"></i> Eliminar', ['delete', 'id' => $model->id], [
                            'class' => 'btn btn-danger btn-sm',
                            'data' => [
                                'confirm' => '¿Está seguro de eliminar esta marca y modelo?',
                                'method' => 'post',
                            ],
                        ]) ?>
                        <?= Html::a('<i class="fa fa-list"></i> Volver a la Lista', ['index'], [
                            'class' => 'btn btn-default btn-sm'
                        ]) ?>
                    </div>
                </div>
                <div class="box-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id:text:ID',
                            'marca:text:Marca',
                            'modelo:text:Modelo',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Información adicional -->
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-mobile"></i> Información de Uso
                    </h3>
                </div>
                <div class="box-body">
                    <div class="alert alert-info">
                        <h4><i class="fa fa-info-circle"></i> Estadísticas</h4>
                        <p>
                            <strong>Teléfonos que usan esta marca y modelo:</strong> <?= $telefonosCount ?>
                        </p>
                        <?php if ($telefonosCount > 0): ?>
                            <p>
                                <em>Esta marca y modelo no puede ser eliminada porque está siendo utilizada por <?= $telefonosCount ?> teléfono(s).</em>
                            </p>
                        <?php else: ?>
                            <p>
                                <em>Esta marca y modelo no está siendo utilizada por ningún teléfono y puede ser eliminada de forma segura.</em>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
