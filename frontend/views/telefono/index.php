<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use common\models\telefono\Telefono;

/* @var $this yii\web\View */
/* @var $searchModel common\models\telefono\TelefonoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Teléfonos';
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss("
@media (max-width: 767px) {
    .telefono-index .hidden-xs { display: none !important; }
    .telefono-index .visible-xs { display: block !important; }
    .telefono-index .btn { width: 100%; margin-bottom: 5px; }
    .telefono-index .box { margin-bottom: 10px; }
}
@media (min-width: 768px) {
    .telefono-index .visible-xs { display: none !important; }
}
.mobile-phone-card {
    margin-bottom: 10px;
}
");
?>

<div class="telefono-index">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-mobile"></i> Teléfonos</h3>
                    <div class="box-tools pull-right">
                        <?= Html::a('<i class="fa fa-upload"></i> <span class="hidden-xs">Insertar Lote</span>', ['batch-insert'], ['class' => 'btn btn-success btn-sm']) ?>
                    </div>
                </div>
                <div class="box-body">
                    <!-- Tabla responsiva para todos los dispositivos -->
                    <div class="table-responsive">
                        <?= GridView::widget([
                            'dataProvider' => $dataProvider,
                            'filterModel' => $searchModel,
                            'columns' => [
                                ['class' => 'yii\grid\SerialColumn'],
                                [
                                    'attribute' => 'imei',
                                    'format' => 'raw',
                                    'value' => function ($model) {
                                        return Html::a($model->imei, ['edit', 'id' => $model->id]);
                                    }
                                ],
                                [
                                    'attribute' => 'marca',
                                    'filter' => ['' => 'Todas las marcas'] + \yii\helpers\ArrayHelper::map($marcas, 'marca', 'marca'),
                                ],
                                [
                                    'attribute' => 'modelo',
                                    'filter' => ['' => 'Todos los modelos'] + \yii\helpers\ArrayHelper::map($modelos, 'modelo', 'modelo'),
                                ],
                                [
                                    'attribute' => 'telefono_compra_id',
                                    'label' => 'Suplidor',
                                    'value' => function ($model) {
                                        return $model->telefonoCompra ? $model->telefonoCompra->suplidor : 'N/A';
                                    },
                                    'filter' => \yii\helpers\ArrayHelper::map(\common\models\telefono\TelefonoCompra::find()->select('suplidor')->distinct()->all(), 'suplidor', 'suplidor'),
                                ],
                                [
                                    'attribute' => 'status',
                                    'format' => 'raw',
                                    'value' => function ($model) {
                                        $statusLabels = [
                                            Telefono::STATUS_EN_TIENDA => ['class' => 'label-success', 'label' => 'En Tienda'],
                                            Telefono::STATUS_VENDIDO => ['class' => 'label-warning', 'label' => 'Vendido'],
                                            Telefono::STATUS_PAGADO => ['class' => 'label-info', 'label' => 'Pagado'],
                                            Telefono::STATUS_DEVUELTO => ['class' => 'label-danger', 'label' => 'Devuelto'],
                                            Telefono::STATUS_IN_DRAFT => ['class' => 'label-default', 'label' => 'En Borrador'],
                                        ];

                                        $status = $statusLabels[$model->status] ?? ['class' => 'label-default', 'label' => ucfirst($model->status)];
                                        return Html::tag('span', $status['label'], ['class' => 'label ' . $status['class']]);
                                    },
                                    'filter' => ['' => 'Todos'] + Telefono::getStatusOptions(),
                                    'contentOptions' => ['class' => 'text-center'],
                                ],
                                [
                                    'attribute' => 'socio_id',
                                    'label' => 'Socio',
                                    'value' => function ($model) {
                                        return $model->socio ? $model->socio->nombre : 'Sin socio';
                                    },
                                    'filter' => ['' => 'Todos los socios'] + \yii\helpers\ArrayHelper::map(\common\models\telefono\TelefonoSocio::find()->all(), 'id', 'nombre'),
                                    'contentOptions' => ['class' => 'text-center'],
                                ],
                                [
                                    'attribute' => 'precio_adquisicion',
                                    'label' => 'Adquisición RD$',
                                    'value' => function ($model) {
                                        return number_format($model->precio_adquisicion, 2);
                                    },
                                    'contentOptions' => ['class' => 'text-right'],
                                ],
                                [
                                    'label' => 'Gastos RD$',
                                    'value' => function ($model) {
                                        return number_format($model->getTotalGastos(), 2);
                                    },
                                    'contentOptions' => ['class' => 'text-right text-danger'],
                                ],
                                [
                                    'attribute' => 'precio_venta_recomendado',
                                    'label' => 'Venta RD$',
                                    'value' => function ($model) {
                                        return number_format($model->precio_venta_recomendado, 2);
                                    },
                                    'contentOptions' => ['class' => 'text-right'],
                                ],
                                [
                                    'attribute' => 'ganancia_absoluta',
                                    'label' => 'Ganancia Neta RD$',
                                    'value' => function ($model) {
                                        return number_format(\common\services\telefono\GananciaService::calcular($model)['neta'], 2);
                                    },
                                    'contentOptions' => ['class' => 'text-right text-success'],
                                ],
                                [
                                    'attribute' => 'ganancia_socio',
                                    'label' => 'Gan. Socio RD$',
                                    'value' => function ($model) {
                                        $ganancia = \common\services\telefono\GananciaService::calcular($model);
                                        return number_format($ganancia['socio'], 2) . ' (' . $ganancia['porcentajeSocio'] . '%)';
                                    },
                                    'contentOptions' => ['class' => 'text-right text-warning'],
                                ],
                                [
                                    'attribute' => 'ganancia_empresa',
                                    'label' => 'Gan. Empresa RD$',
                                    'value' => function ($model) {
                                        $ganancia = \common\services\telefono\GananciaService::calcular($model);
                                        return number_format($ganancia['empresa'], 2) . ' (' . $ganancia['porcentajeEmpresa'] . '%)';
                                    },
                                    'contentOptions' => ['class' => 'text-right text-info'],
                                ],
                                [
                                    'attribute' => 'fecha_ingreso',
                                    'format' => 'date',
                                    'contentOptions' => ['class' => 'text-center'],
                                ],
                                [
                                    'class' => 'yii\grid\ActionColumn',
                                    'template' => '{edit} {gastos}',
                                    'buttons' => [
                                        'edit' => function ($url, $model, $key) {
                                            return Html::a('<i class="fa fa-pencil"></i>', ['edit', 'id' => $model->id], [
                                                'class' => 'btn btn-xs btn-primary',
                                                'title' => 'Editar Teléfono',
                                            ]);
                                        },
                                        'gastos' => function ($url, $model, $key) {
                                            return Html::a('<i class="fa fa-money"></i>', ['telefono-gasto/index', 'telefono_id' => $model->id], [
                                                'class' => 'btn btn-xs btn-info',
                                                'title' => 'Ver/Añadir Gastos',
                                            ]);
                                        },
                                    ],
                                ],
                            ],
                            'tableOptions' => ['class' => 'table table-striped table-bordered'],
                            'summary' => 'Mostrando <b>{begin}-{end}</b> de <b>{totalCount}</b> teléfonos.',
                            'emptyText' => 'No se encontraron teléfonos.',
                        ]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->render('_desglose-modal') ?>