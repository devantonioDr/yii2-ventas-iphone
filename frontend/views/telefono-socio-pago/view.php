<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use common\services\telefono\GananciaService;

/** @var yii\web\View $this */
/** @var common\models\telefono\TelefonoSocioPago $model */

$this->title = 'Detalle de Pago: ' . $model->codigo_factura;
$this->params['breadcrumbs'][] = ['label' => 'Pagos a Socios', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

// Totales para el pie de página
$total_precio_adquisicion = 0;
$total_gastos = 0;
$total_precio_venta_recomendado = 0;
$total_ganancia_neta = 0;
$total_ganancia_socio = 0;
$total_ganancia_empresa = 0;

foreach ($model->telefonos as $telefono) {
    $total_precio_adquisicion += $telefono->precio_adquisicion;
    $total_gastos += $telefono->getTotalGastos();
    $total_precio_venta_recomendado += $telefono->precio_venta_recomendado;
    $ganancia = GananciaService::calcular($telefono);
    $total_ganancia_neta += $ganancia['neta'];
    $total_ganancia_socio += $ganancia['socio'];
    $total_ganancia_empresa += $ganancia['empresa'];
}
?>
<div class="telefono-socio-pago-view">

    <div class="row">
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Información General</h3>
                    <div class="box-tools pull-right">
                        <?= Html::a('<i class="fa fa-undo"></i> Revertir', ['revertir', 'id' => $model->id], [
                            'class' => 'btn btn-danger btn-xs',
                            'data' => ['confirm' => '¿Está seguro?', 'method' => 'post'],
                        ]) ?>
                        <button type="button" class="btn btn-box-tool" onclick="window.print();" title="Imprimir"><i class="fa fa-print"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <dl class="dl-horizontal">
                        <dt>Código Factura</dt>
                        <dd><?= Html::encode($model->codigo_factura) ?></dd>
                        <dt>Socio</dt>
                        <dd><?= Html::encode($model->socio->nombre) ?></dd>
                        <dt>Fecha de Pago</dt>
                        <dd><?= Yii::$app->formatter->asDatetime($model->fecha_pago) ?></dd>
                        <dt>Teléfonos Pagados</dt>
                        <dd><?= $model->cantidad_telefonos ?></dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-lg-8 col-md-6 col-sm-12">
            <div class="row">
                <div class="col-lg-4 col-md-12 col-sm-6">
                    <div class="info-box bg-green">
                        <span class="info-box-icon"><i class="fa fa-money"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Pagado al Socio</span>
                            <span class="info-box-number"><?= Yii::$app->formatter->asCurrency($model->invertido + $model->ganancia_socio) ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12 col-sm-6">
                    <div class="info-box bg-aqua">
                        <span class="info-box-icon"><i class="fa fa-line-chart"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Ganancia Neta</span>
                            <span class="info-box-number"><?= Yii::$app->formatter->asCurrency($model->ganancia_neta) ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12 col-sm-6">
                    <div class="info-box bg-yellow">
                        <span class="info-box-icon"><i class="fa fa-user"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Beneficio del Socio</span>
                            <span class="info-box-number"><?= Yii::$app->formatter->asCurrency($model->ganancia_socio) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Desglose de Teléfonos en este Pago</h3>
        </div>
        <div class="box-body table-responsive">
            <?= GridView::widget([
                'dataProvider' => new ArrayDataProvider([
                    'allModels' => $model->telefonos,
                    'pagination' => false,
                ]),
                'summary' => '',
                'showFooter' => true,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'marca',
                    'modelo',
                    [
                        'attribute' => 'imei',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return Html::a($model->imei, ['telefono/edit', 'id' => $model->id]);
                        }
                    ],
                    [
                        'attribute' => 'precio_adquisicion',
                        'format' => 'currency',
                        'footer' => '<b>' . Yii::$app->formatter->asCurrency($total_precio_adquisicion) . '</b>',
                        'contentOptions' => ['class' => 'text-right'],
                        'footerOptions' => ['class' => 'text-right'],
                    ],
                    [
                        'label' => 'Gastos',
                        'value' => function ($telefono) { return $telefono->getTotalGastos(); },
                        'format' => 'currency',
                        'footer' => '<b>' . Yii::$app->formatter->asCurrency($total_gastos) . '</b>',
                        'contentOptions' => ['class' => 'text-right text-danger'],
                         'footerOptions' => ['class' => 'text-right text-danger'],
                    ],
                    [
                        'attribute' => 'precio_venta_recomendado',
                        'format' => 'currency',
                        'footer' => '<b>' . Yii::$app->formatter->asCurrency($total_precio_venta_recomendado) . '</b>',
                        'contentOptions' => ['class' => 'text-right'],
                        'footerOptions' => ['class' => 'text-right'],
                    ],
                    [
                        'label' => 'Ganancia Neta',
                        'value' => function ($telefono) { return GananciaService::calcular($telefono)['neta']; },
                        'format' => 'currency',
                        'footer' => '<b>' . Yii::$app->formatter->asCurrency($total_ganancia_neta) . '</b>',
                        'contentOptions' => ['class' => 'text-right text-success', 'style' => 'font-weight:bold'],
                        'footerOptions' => ['class' => 'text-right text-success', 'style' => 'font-weight:bold'],
                    ],
                    [
                        'label' => 'Ganancia Socio',
                        'value' => function ($telefono) {
                            $ganancia = GananciaService::calcular($telefono);
                            return $ganancia['socio'] . ' (' . $ganancia['porcentajeSocio'] . '%)';
                        },
                        'format' => 'raw',
                        'footer' => '<b>' . Yii::$app->formatter->asCurrency($total_ganancia_socio) . '</b>',
                        'contentOptions' => ['class' => 'text-right text-warning'],
                        'footerOptions' => ['class' => 'text-right text-warning'],
                    ],
                    [
                        'label' => 'Ganancia Empresa',
                        'value' => function ($telefono) {
                            $ganancia = GananciaService::calcular($telefono);
                             return $ganancia['empresa'] . ' (' . $ganancia['porcentajeEmpresa'] . '%)';
                        },
                        'format' => 'raw',
                        'footer' => '<b>' . Yii::$app->formatter->asCurrency($total_ganancia_empresa) . '</b>',
                        'contentOptions' => ['class' => 'text-right text-info'],
                        'footerOptions' => ['class' => 'text-right text-info'],
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div> 