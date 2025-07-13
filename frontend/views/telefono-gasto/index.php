<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $telefono common\models\telefono\Telefono */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = "{$telefono->marca} {$telefono->modelo} (IMEI: {$telefono->imei})";
$this->params['breadcrumbs'][] = ['label' => 'Teléfonos', 'url' => ['/telefono/index']];
$this->params['breadcrumbs'][] = ['label' => 'Editar', 'url' => ['/telefono/edit', 'id' => $telefono->id]];
$this->params['breadcrumbs'][] = 'Gastos';
?>

<div class="telefono-gasto-index">
    <div class="row">
        <div class="col-xs-12">
            <!-- Total de gastos -->
            <div class="alert alert-info" style="margin-bottom: 20px;">
                <strong>Total de gastos:</strong>
                RD$ <?= number_format($telefono->totalGastos, 2) ?>
            </div>
        </div>

        <!-- Botón para añadir gasto y volver -->
        <div class="col-xs-12" style="margin-bottom: 20px;">
            <?= Html::a('<i class="fa fa-plus"></i> Añadir Gasto', ['create', 'telefono_id' => $telefono->id], [
                'class' => 'btn btn-success'
            ]) ?>
            <?= Html::a('<i class="fa fa-arrow-left"></i> Volver al Teléfono', ['/telefono/edit', 'id' => $telefono->id], [
                'class' => 'btn btn-default'
            ]) ?>
        </div>

        <!-- Lista de gastos -->
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-list"></i> Lista de Gastos
                    </h3>
                </div>
                <div class="box-body">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
                            [
                                'attribute' => 'descripcion',
                                'label' => 'Descripción',
                            ],
                            [
                                'attribute' => 'monto_gasto',
                                'label' => 'Monto',
                                'value' => function ($model) {
                                    return 'RD$ ' . number_format($model->monto_gasto, 2);
                                },
                            ],
                            [
                                'attribute' => 'fecha_gasto',
                                'label' => 'Fecha',
                                'format' => ['datetime', 'php:d/m/Y H:i'],
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{update} {delete}',
                                'buttons' => [
                                    'update' => function ($url, $model) {
                                        return Html::a('<i class="fa fa-pencil"></i>', ['update', 'id' => $model->id], [
                                            'class' => 'btn btn-xs btn-primary',
                                            'title' => 'Editar',
                                        ]);
                                    },
                                    'delete' => function ($url, $model) {
                                        return Html::a('<i class="fa fa-trash"></i>', ['delete', 'id' => $model->id], [
                                            'class' => 'btn btn-xs btn-danger',
                                            'title' => 'Eliminar',
                                            'data' => [
                                                'confirm' => '¿Está seguro de eliminar este gasto?',
                                                'method' => 'post',
                                            ],
                                        ]);
                                    },
                                ],
                            ],
                        ],
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div> 