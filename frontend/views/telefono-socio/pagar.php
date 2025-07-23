<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use common\services\telefono\GananciaService;

/** @var yii\web\View $this */
/** @var common\models\telefono\TelefonoSocio $model */
/** @var array $ganancia */
/** @var float $montoTotalAPagar */
/** @var common\models\telefono\Telefono[] $telefonos */

$this->title = 'Confirmar Pago a Socio: ' . $model->nombre;
$this->params['breadcrumbs'][] = ['label' => 'Socios', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$total_precio_venta_recomendado = 0;
foreach ($telefonos as $telefono) {
    $total_precio_venta_recomendado += $telefono->precio_venta_recomendado;
}
?>

<div class="telefono-socio-pagar">

    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Resumen de Pago</h3>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table">
                            <tr>
                                <th style="width:50%">Inversión Socio:</th>
                                <td><?= Yii::$app->formatter->asCurrency($ganancia['precioAdquisicion']) ?></td>
                            </tr>
                            <tr>
                                <th>Beneficio Socio:</th>
                                <td><?= Yii::$app->formatter->asCurrency($ganancia['socio']) ?></td>
                            </tr>
                            <tr>
                                <th><strong>Total a Pagar:</strong></th>
                                <td><strong><?= Yii::$app->formatter->asCurrency($montoTotalAPagar) ?></strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-9 col-md-6 col-sm-12">
            <div class="row">
                 <div class="col-lg-4 col-md-12 col-sm-6">
                    <div class="info-box bg-aqua">
                        <span class="info-box-icon"><i class="fa fa-line-chart"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Ganancia Neta Total</span>
                            <span class="info-box-number"><?= Yii::$app->formatter->asCurrency($ganancia['neta']) ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12 col-sm-6">
                    <div class="info-box bg-red">
                        <span class="info-box-icon"><i class="fa fa-money"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Gastos</span>
                            <span class="info-box-number"><?= Yii::$app->formatter->asCurrency($ganancia['gastos']) ?></span>
                        </div>
                    </div>
                </div>
                 <div class="col-lg-4 col-md-12 col-sm-6">
                    <div class="info-box bg-green">
                        <span class="info-box-icon"><i class="fa fa-briefcase"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Ganancia Empresa</span>
                            <span class="info-box-number"><?= Yii::$app->formatter->asCurrency($ganancia['empresa']) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Teléfonos a Pagar</h3>
        </div>
        <div class="box-body table-responsive">
             <?= GridView::widget([
                'dataProvider' => new ArrayDataProvider([
                    'allModels' => $telefonos,
                    'pagination' => false,
                ]),
                'summary' => '',
                'showFooter' => true,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn', 'footer' => '<b>Totales:</b>'],
                    'marca',
                    'modelo',
                    'imei',
                    [
                        'attribute' => 'precio_adquisicion',
                        'format' => 'currency',
                        'contentOptions' => ['class' => 'text-right'],
                        'footer' => '<b>'.Yii::$app->formatter->asCurrency($ganancia['precioAdquisicion']).'</b>',
                        'footerOptions' => ['class' => 'text-right'],
                    ],
                    [
                        'label' => 'Gastos',
                        'value' => function ($telefono) { return $telefono->getTotalGastos(); },
                        'format' => 'currency',
                        'contentOptions' => ['class' => 'text-right text-danger'],
                        'footer' => '<b>'.Yii::$app->formatter->asCurrency($ganancia['gastos']).'</b>',
                        'footerOptions' => ['class' => 'text-right text-danger'],
                    ],
                    [
                        'attribute' => 'precio_venta_recomendado',
                        'format' => 'currency',
                        'contentOptions' => ['class' => 'text-right'],
                        'footer' => '<b>'.Yii::$app->formatter->asCurrency($total_precio_venta_recomendado).'</b>',
                        'footerOptions' => ['class' => 'text-right'],
                    ],
                    [
                        'label' => 'Ganancia Neta',
                        'value' => function ($telefono) { return GananciaService::calcular($telefono)['neta']; },
                        'format' => 'currency',
                        'contentOptions' => ['class' => 'text-right text-success', 'style' => 'font-weight:bold'],
                        'footer' => '<b>'.Yii::$app->formatter->asCurrency($ganancia['neta']).'</b>',
                        'footerOptions' => ['class' => 'text-right text-success', 'style' => 'font-weight:bold'],
                    ],
                    [
                        'label' => 'Ganancia Socio',
                        'value' => function ($telefono) {
                            $g = GananciaService::calcular($telefono);
                            return $g['socio'] . ' (' . $g['porcentajeSocio'] . '%)';
                        },
                        'format' => 'raw',
                        'contentOptions' => ['class' => 'text-right text-warning'],
                        'footer' => '<b>'.Yii::$app->formatter->asCurrency($ganancia['socio']).'</b>',
                        'footerOptions' => ['class' => 'text-right text-warning'],
                    ],
                    [
                        'label' => 'Ganancia Empresa',
                        'value' => function ($telefono) {
                             $g = GananciaService::calcular($telefono);
                            return $g['empresa'] . ' (' . $g['porcentajeEmpresa'] . '%)';
                        },
                        'format' => 'raw',
                        'contentOptions' => ['class' => 'text-right text-info'],
                        'footer' => '<b>'.Yii::$app->formatter->asCurrency($ganancia['empresa']).'</b>',
                        'footerOptions' => ['class' => 'text-right text-info'],
                    ],
                ],
            ]); ?>
        </div>
         <div class="box-footer no-print">
            <div class="row">
                <div class="col-xs-12">
                    <?php $form = ActiveForm::begin(['options' => ['class' => 'pull-right']]); ?>
                    <?= Html::submitButton('<i class="fa fa-credit-card"></i> Confirmar y Realizar Pago', ['class' => 'btn btn-success btn-lg']) ?>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>