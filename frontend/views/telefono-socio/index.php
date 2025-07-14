<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\services\telefono\GananciaService;

/* @var $this yii\web\View */
/* @var $searchModel common\models\telefono\TelefonoSocioSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Socios de Teléfonos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Listado de Socios</h3>
                <div class="box-tools pull-right">
                    <?= Html::a('<i class="fa fa-plus"></i> Crear Socio', ['create'], [
                        'class' => 'btn btn-success btn-sm btn-flat'
                    ]) ?>
                </div>
            </div>

            <div class="box-body">
                <div class="table-responsive">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'tableOptions' => ['class' => 'table table-striped table-bordered'],
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        // 'id',
                        'nombre',
                        [
                            'attribute' => 'margen_ganancia',
                            'value' => function ($model) {
                                return number_format($model->margen_ganancia, 2) . '%';
                            },
                            'filter' => false,
                        ],
                        // [
                        //     'label' => 'Monto Invertido',
                        //     'value' => function ($model) use ($ganciasSocios) {
                        //         $ganancia = $ganciasSocios[$model->id] ?? [];
                        //         return 'RD$ ' . number_format($ganancia['precioAdquisicion'] ?? 0, 2);
                        //     },
                        //     'filter' => false,
                        // ],
                        // [
                        //     'label' => 'Ganancia Pendiente Socio',
                        //     'value' => function ($model) use ($ganciasSocios) {
                        //         $ganancia = $ganciasSocios[$model->id] ?? [];
                        //         return 'RD$ ' . number_format($ganancia['socio'] ?? 0, 2);
                        //     },
                        //     'filter' => false,
                        // ],
                        // [
                        //     'label' => 'Ganancia Pendiente Empresa',
                        //     'value' => function ($model) use ($ganciasSocios) {
                        //         $ganancia = $ganciasSocios[$model->id] ?? [];
                        //         return 'RD$ ' . number_format($ganancia['empresa'] ?? 0, 2);
                        //     },
                        //     'filter' => false,
                        // ],
                        [
                            'attribute' => 'telefonos_count',
                            'label' => 'Teléfonos Pendientes por Pagar',
                            'value' => function ($model) {
                                return $model->getTelefonosPendientesPorPagar()->count();
                            },
                            'filter' => false,
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{update} {delete} {pagar}',
                            'buttons' => [
                                'update' => function ($url, $model) {
                                    return Html::a('<i class="fa fa-pencil"></i>', $url, [
                                        'class' => 'btn btn-warning btn-xs',
                                        'title' => 'Editar',
                                    ]);
                                },
                                'delete' => function ($url, $model) {
                                    return Html::a('<i class="fa fa-trash"></i>', $url, [
                                        'class' => 'btn btn-danger btn-xs',
                                        'title' => 'Eliminar',
                                        'data' => [
                                            'confirm' => '¿Está seguro de que desea eliminar este socio?',
                                            'method' => 'post',
                                        ],
                                    ]);
                                },
                                'pagar' => function ($url, $model) {
                                    $ganancia = \common\services\telefono\GananciaService::calcularTotalGanaciaPendienteSocio($model->id);
                                    $montoTotalAPagar = $ganancia['precioAdquisicion'] + $ganancia['socio'];
                                    if ($montoTotalAPagar > 0) {
                                        return Html::a('<i class="fa fa-money"></i>', ['pagar', 'id' => $model->id], [
                                            'class' => 'btn btn-success btn-xs',
                                            'title' => 'Pagar',
                                        ]);
                                    }
                                    return '';
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