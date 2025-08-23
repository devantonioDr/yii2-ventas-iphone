<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\telefono\TelefonoMarcaModeloSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Gestión de Marcas y Modelos';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="telefono-marca-modelo-index">
    <div class="row">
        <!-- Botón para añadir nueva marca y modelo -->
        <div class="col-xs-12" style="margin-bottom: 20px;">
            <?= Html::a('<i class="fa fa-plus"></i> Añadir Marca y Modelo', ['create'], [
                'class' => 'btn btn-success'
            ]) ?>
        </div>

        <!-- Lista de marcas y modelos -->
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-list"></i> Lista de Marcas y Modelos
                    </h3>
                </div>
                <div class="box-body">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
                            [
                                'attribute' => 'marca',
                                'label' => 'Marca',
                                'filter' => Html::activeTextInput($searchModel, 'marca', [
                                    'class' => 'form-control',
                                    'placeholder' => 'Buscar marca...'
                                ]),
                            ],
                            [
                                'attribute' => 'modelo',
                                'label' => 'Modelo',
                                'filter' => Html::activeTextInput($searchModel, 'modelo', [
                                    'class' => 'form-control',
                                    'placeholder' => 'Buscar modelo...'
                                ]),
                            ],
                            [
                                'attribute' => 'id',
                                'label' => 'ID Generado',
                                'filter' => false,
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{view} {update} {delete}',
                                'buttons' => [
                                    'view' => function ($url, $model) {
                                        return Html::a('<i class="fa fa-eye"></i>', ['view', 'id' => $model->id], [
                                            'class' => 'btn btn-xs btn-info',
                                            'title' => 'Ver detalles',
                                        ]);
                                    },
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
                                                'confirm' => '¿Está seguro de eliminar esta marca y modelo? Esta acción no se puede deshacer.',
                                                'method' => 'post',
                                            ],
                                        ]);
                                    },
                                ],
                            ],
                        ],
                        'summary' => 'Mostrando {begin}-{end} de {totalCount} marcas y modelos',
                        'emptyText' => 'No se encontraron marcas y modelos',
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>
