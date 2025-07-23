<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use common\services\telefono\GananciaService;
use common\models\telefono\Telefono;

/* @var $this yii\web\View */
/* @var $model common\models\telefono\TelefonoCompra */
/* @var $summary array */
/* @var $telefonosDataProvider yii\data\ActiveDataProvider */

$this->title = 'Detalle de Compra: ' . $model->codigo_factura;
$this->params['breadcrumbs'][] = ['label' => 'Compras de Teléfonos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="telefono-compra-view">

    <div class="row">
        <div class="col-md-4">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Detalles de la Compra</h3>
                </div>
                <div class="box-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'codigo_factura',
                            'fecha_compra:date',
                            'suplidor',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-4">
                    <div class="info-box bg-red">
                        <span class="info-box-icon"><i class="fa fa-shopping-cart"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Invertido</span>
                            <span class="info-box-number"><?= Yii::$app->formatter->asCurrency($summary['invertido']) ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box bg-aqua">
                        <span class="info-box-icon"><i class="fa fa-line-chart"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Ganancia Neta</span>
                            <span class="info-box-number"><?= Yii::$app->formatter->asCurrency($summary['ganancia_neta_total']) ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box bg-green">
                        <span class="info-box-icon"><i class="fa fa-briefcase"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Ganancia Empresa</span>
                            <span class="info-box-number"><?= Yii::$app->formatter->asCurrency($summary['ganancia_empresa_total']) ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box bg-orange">
                        <span class="info-box-icon"><i class="fa fa-user"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Ganancia Socio</span>
                            <span class="info-box-number"><?= Yii::$app->formatter->asCurrency($summary['ganancia_socio_total']) ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box bg-yellow">
                        <span class="info-box-icon"><i class="fa fa-money"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Gastos</span>
                            <span class="info-box-number"><?= Yii::$app->formatter->asCurrency($summary['gasto_total']) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-mobile"></i> Teléfonos de esta Compra</h3>
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <?= GridView::widget([
                    'dataProvider' => $telefonosDataProvider,
                    'showFooter' => true,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        'imei',
                        'marca',
                        'modelo',
                        [
                            'attribute' => 'socio_id',
                            'label' => 'Socio',
                            'value' => function ($model) {
                                return $model->socio ? $model->socio->nombre : 'Sin socio';
                            },
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
                            }
                        ],
                        [
                            'attribute' => 'precio_adquisicion',
                            'format' => 'currency',
                            'contentOptions' => ['class' => 'text-right'],
                            'footer' => '<b>' . Yii::$app->formatter->asCurrency($summary['invertido']) . '</b>',
                            'footerOptions' => ['class' => 'text-right'],
                        ],
                        [
                            'label' => 'Gastos',
                            'value' => function ($model) {
                                return $model->getTotalGastos();
                            },
                            'format' => 'currency',
                            'contentOptions' => ['class' => 'text-right text-danger'],
                            'footer' => '<b>' . Yii::$app->formatter->asCurrency($summary['gasto_total']) . '</b>',
                            'footerOptions' => ['class' => 'text-right text-danger'],
                        ],
                        [
                            'label' => 'Ganancia Neta',
                            'value' => function ($model) {
                                return GananciaService::calcular($model)['neta'];
                            },
                            'format' => 'currency',
                            'contentOptions' => ['class' => 'text-right text-success'],
                            'footer' => '<b>' . Yii::$app->formatter->asCurrency($summary['ganancia_neta_total']) . '</b>',
                            'footerOptions' => ['class' => 'text-right text-success', 'style' => 'font-weight:bold'],
                        ],
                    ],
                    'summary' => 'Mostrando <b>{begin}-{end}</b> de <b>{totalCount}</b> teléfonos.',
                    'emptyText' => 'No hay teléfonos en esta compra.',
                ]); ?>
            </div>
        </div>
    </div>
</div>