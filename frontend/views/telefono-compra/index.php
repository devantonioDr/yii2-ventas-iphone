<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use common\services\compra\CompraService;

/* @var $this yii\web\View */
/* @var $searchModel common\models\telefono\TelefonoCompraSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Compras de Teléfonos';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="telefono-compra-index">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-shopping-cart"></i> <?= Html::encode($this->title) ?></h3>
        </div>
        <div class="box-body">
            
            <div class="table-responsive">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        [
                            'attribute' => 'codigo_factura',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return Html::a($model->codigo_factura, ['view', 'id' => $model->id]);
                            }
                        ],
                        [
                            'attribute' => 'fecha_compra',
                            'format' => 'date',
                            'headerOptions' => ['class' => 'text-center'],
                            'contentOptions' => ['class' => 'text-center'],
                            'filter' => \yii\jui\DatePicker::widget([
                                'model' => $searchModel,
                                'attribute' => 'fecha_compra',
                                'language' => 'es',
                                'dateFormat' => 'yyyy-MM-dd',
                                'options' => ['class' => 'form-control'],
                            ]),
                        ],
                        'suplidor',
                        [
                            'label' => 'Invertido RD$',
                            'value' => function ($model) {
                                $summary = CompraService::getSummary($model);
                                return number_format($summary['invertido'], 2);
                            },
                            'contentOptions' => ['class' => 'text-right'],
                        ],
                        [
                            'label' => 'Gastos RD$',
                            'value' => function ($model) {
                                $summary = CompraService::getSummary($model);
                                return number_format($summary['gasto_total'], 2);
                            },
                            'contentOptions' => ['class' => 'text-right text-danger'],
                        ],
                        [
                            'label' => 'Ganancia Neta RD$',
                            'value' => function ($model) {
                                $summary = CompraService::getSummary($model);
                                return number_format($summary['ganancia_neta_total'], 2);
                            },
                            'contentOptions' => ['class' => 'text-right text-success'],
                        ],
                        [
                            'label' => 'Gan. Empresa RD$',
                            'value' => function ($model) {
                                $summary = CompraService::getSummary($model);
                                return number_format($summary['ganancia_empresa_total'], 2);
                            },
                            'contentOptions' => ['class' => 'text-right text-info'],
                        ],
                        [
                            'label' => 'Gan. Socio RD$',
                            'value' => function ($model) {
                                $summary = CompraService::getSummary($model);
                                return number_format($summary['ganancia_socio_total'], 2);
                            },
                            'contentOptions' => ['class' => 'text-right text-warning'],
                        ],
                        [
                            'label' => 'Teléfonos',
                            'value' => function ($model) {
                                return count($model->telefonos);
                            },
                            'contentOptions' => ['class' => 'text-center'],
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{view}',
                            'buttons' => [
                                'view' => function ($url, $model, $key) {
                                    return Html::a('<i class="fa fa-eye"></i>', ['view', 'id' => $model->id], [
                                        'class' => 'btn btn-xs btn-info',
                                        'title' => 'Ver Detalles de la Compra',
                                    ]);
                                },
                            ],
                        ],
                    ],
                    'summary' => 'Mostrando <b>{begin}-{end}</b> de <b>{totalCount}</b> compras.',
                    'emptyText' => 'No se encontraron compras.',
                ]); ?>
            </div>
        </div>
    </div>
</div> 