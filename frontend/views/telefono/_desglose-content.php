<?php
use yii\helpers\Html;
use common\services\telefono\GananciaService;

/** @var $telefono common\models\telefono\Telefono */
/** @var $gastos array */

$ganancia = GananciaService::calcular($telefono);
?>

<div class="desglose-factura">
    <div class="header text-center">
        <h3>Desglose de Ganancia</h3>
        <p>
            <b><?= Html::encode($telefono->marca . ' ' . $telefono->modelo) ?></b><br>
            IMEI: <?= Html::encode($telefono->imei) ?>
        </p>
    </div>

    <?php if ($telefono->telefonoCompra): ?>
    <div class="seccion">
        <h4>Factura de Compra</h4>
        <div class="item">
            <span>Código</span>
            <span class="text-right">
                <?= Html::a(
                    Html::encode($telefono->telefonoCompra->codigo_factura),
                    ['/telefono-compra/view', 'id' => $telefono->telefono_compra_id],
                    ['target' => '_blank', 'data-pjax' => 0]
                ) ?>
            </span>
        </div>
        <div class="item">
            <span>Suplidor</span>
            <span class="text-right">
                <?= Html::encode($telefono->telefonoCompra->suplidor) ?>
            </span>
        </div>
    </div>
    <?php endif; ?>

    <div class="seccion">
        <h4>Gastos</h4>
        <?php if (count($gastos)): ?>
            <ul class="list-unstyled">
                <?php foreach ($gastos as $gasto): ?>
                    <li class="item">
                        <span><?= Html::encode($gasto['descripcion']) ?></span>
                        <span class="text-right">+RD$ <?= number_format($gasto['monto_gasto'], 2) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-muted">No hay gastos registrados.</p>
        <?php endif; ?>
        <div class="total">
            Total Gastos: <span class="text-right">RD$ <?= number_format($telefono->getTotalGastos(), 2) ?></span>
        </div>
    </div>

    <div class="seccion">
        <h4>Precio de Adquisición</h4>
        <div class="item">
            <span>Adquisición</span>
            <span class="text-right">RD$ <?= number_format($telefono->precio_adquisicion, 2) ?></span>
        </div>
    </div>

    <div class="seccion">
        <h4>Precio de Venta</h4>
        <div class="item">
            <span>Venta</span>
            <span class="text-right">RD$ <?= number_format($telefono->precio_venta_recomendado, 2) ?></span>
        </div>
    </div>

    <div class="seccion">
        <h4>Cálculo de Ganancia</h4>
        <p class="text-muted small"><i>Después de deducir todos los gastos.</i></p>
        <div class="item">
            <span><b>Ganancia Total</b></span>
            <span class="text-right"><b>RD$ <?= number_format($ganancia['neta'], 2) ?></b></span>
        </div>
        <div class="item">
            <span>Ganancia Socio (<?= $ganancia['porcentajeSocio'] ?>%)</span>
            <span class="text-right text-warning">RD$ <?= number_format($ganancia['socio'], 2) ?></span>
        </div>
        <div class="item">
            <span>Ganancia Empresa (<?= $ganancia['porcentajeEmpresa'] ?>%)</span>
            <span class="text-right text-success">RD$ <?= number_format($ganancia['empresa'], 2) ?></span>
        </div>
    </div>
</div> 