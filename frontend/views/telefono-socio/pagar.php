<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\services\telefono\GananciaService;

/** @var yii\web\View $this */
/** @var common\models\telefono\TelefonoSocio $model */
/** @var array $ganancia */
/** @var float $montoTotalAPagar */
/** @var common\models\telefono\Telefono[] $telefonos */

$this->title = 'Factura de Pago a Socio: ' . $model->nombre;
$this->params['breadcrumbs'][] = ['label' => 'Socios', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Factura';
?>

<!-- info row -->
<div class="box box-primary">

    <div class="box-header with-border">
    </div>
    <div class="box-body">
        <!-- Table row -->
        <div class="row">
            <div class="col-xs-12 table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>IMEI</th>
                            <th>Teléfono</th>
                            <th>Precio Adquisición</th>
                            <th>Gastos</th>
                            <th>Precio Venta</th>
                            <th>Ganancia Neta</th>
                            <th>Ganancia Socio</th>
                            <th>Ganancia Empresa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($telefonos as $telefono): ?>
                            <?php $gananciaTelefono = GananciaService::calcular($telefono); ?>
                            <tr>
                                <td><?= Html::encode($telefono->imei) ?></td>
                                <td><?= Html::encode($telefono->marca . ' ' . $telefono->modelo) ?></td>
                                <td><?= 'RD$ ' . number_format($telefono->precio_adquisicion, 2) ?></td>
                                <td><?= 'RD$ ' . number_format($telefono->getTotalGastos(), 2) ?></td>
                                <td><?= 'RD$ ' . number_format($telefono->precio_venta_recomendado, 2) ?></td>
                                <td><?= 'RD$ ' . number_format($gananciaTelefono['neta'], 2) ?></td>
                                <td><?= 'RD$ ' . number_format($gananciaTelefono['socio'], 2) ?></td>
                                <td><?= 'RD$ ' . number_format($gananciaTelefono['empresa'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-6">
                <p class="lead">Resumen de Pago:</p>
                <div class="table-responsive">
                    <table class="table">
                        <tr>
                            <th style="width:50%">Total Adquisición:</th>
                            <td><?= 'RD$ ' . number_format($ganancia['precioAdquisicion'], 2) ?></td>
                        </tr>
                        <tr>
                            <th>Total Gastos:</th>
                            <td><?= 'RD$ ' . number_format($ganancia['gastos'], 2) ?></td>
                        </tr>
                        <tr>
                            <th>Ganancia Neta Total:</th>
                            <td><?= 'RD$ ' . number_format($ganancia['neta'], 2) ?></td>
                        </tr>
                        <tr>
                            <th>Ganancia Total Socio:</th>
                            <td><?= 'RD$ ' . number_format($ganancia['socio'], 2) ?></td>
                        </tr>
                        <tr>
                            <th><strong>Monto Total a Pagar:</strong></th>
                            <td><strong><?= 'RD$ ' . number_format($montoTotalAPagar, 2) ?></strong></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- this row will not appear when printing -->
        <div class="row no-print">
            <div class="col-xs-12">
                <?php $form = ActiveForm::begin(['options' => ['class' => 'pull-right']]); ?>
                <?= Html::submitButton('<i class="fa fa-credit-card"></i> Confirmar Pago', ['class' => 'btn btn-success']) ?>

                <button type="button" class="btn btn-primary" style="margin-right: 5px;" onclick="window.print();">
                    <i class="fa fa-print"></i> Imprimir
                </button>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
</div>