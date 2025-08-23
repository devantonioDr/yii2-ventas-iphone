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
                            'format' => 'raw',
                            'value' => function ($model) {
                                $count = $model->getTelefonosPendientesPorPagar()->count();
                                return Html::a($count, ['pagar', 'id' => $model->id], [
                                    'title' => 'Ir a pagar'
                                ]);
                            },
                            'contentOptions' => ['class' => 'text-center'],
                            'filter' => false,
                        ],
                        [
                            'label' => 'Balance Disponible',
                            'format' => 'raw',
                            'value' => function ($model) {
                                $balance = 0;
                                if ($model->wallet) {
                                    $balance = (float)$model->wallet->balance;
                                } else {
                                    try {
                                        $balance = (new \common\usecases\wallet\GetAvailableBalanceUseCase($model->id))->execute();
                                    } catch (\Throwable $e) {
                                        $balance = 0;
                                    }
                                }
                                return Html::a('RD$ ' . number_format($balance, 2), ['wallet/index', 'socio_id' => $model->id]);
                            },
                            'contentOptions' => ['class' => 'text-right'],
                            'filter' => false,
                        ],
                        [
                            'label' => 'Pagos',
                            'format' => 'raw',
                            'value' => function ($model) {
                                $count = $model->getTelefonoSocioPagos()->count();
                                return Html::a($count, ['/telefono-socio-pago/index', 'TelefonoSocioPagoSearch[socio_id]' => $model->id]);
                            },
                            'contentOptions' => ['class' => 'text-center'],
                            'filter' => false,
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{update} {pagar}',
                            'buttons' => [
                                'update' => function ($url, $model) {
                                    return Html::a('<i class="fa fa-pencil"></i>', $url, [
                                        'class' => 'btn btn-warning btn-xs',
                                        'title' => 'Editar',
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