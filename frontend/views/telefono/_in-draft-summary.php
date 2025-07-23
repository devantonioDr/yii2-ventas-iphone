<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $inDraftSummary array */

if (!empty($inDraftSummary)) {
    $total_cantidad = array_sum(array_column($inDraftSummary, 'cantidad'));
    $total_precio_adquisicion = array_sum(array_column($inDraftSummary, 'precio_adquisicion_total'));
    $total_precio_venta_recomendado = array_sum(array_column($inDraftSummary, 'precio_venta_recomendado_total'));
    $total_ganancia_neta = array_sum(array_column($inDraftSummary, 'ganancia_neta_total'));
    $total_ganancia_socio = array_sum(array_column($inDraftSummary, 'ganancia_socio_total'));
    $total_ganancia_empresa = array_sum(array_column($inDraftSummary, 'ganancia_empresa_total'));
}

?>
<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">Resumen de Teléfonos en Borrador (In Draft)</h3>
        <div class="box-tools pull-right">
            <?= Html::button('<i class="fa fa-arrow-right"></i> Mover todo a Inventario', [
                'class' => 'btn btn-success btn-sm',
                'data-toggle' => 'modal',
                'data-target' => '#suplidor-modal',
            ]) ?>
        </div>
    </div>
    <div class="box-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" style="min-width: 1200px;">
                <thead>
                    <tr>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Socio</th>
                        <th class="text-center">Cantidad</th>
                        <th class="text-right">Invertido</th>
                        <th class="text-right">Venta</th>
                        <th class="text-right">Ganancia Neta</th>
                        <th class="text-right">Ganancia Socio</th>
                        <th class="text-right">Ganancia Empresa</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inDraftSummary as $summary): ?>
                        <tr>
                            <td><?= Html::encode($summary['marca']) ?></td>
                            <td><?= Html::encode($summary['modelo']) ?></td>
                            <td><?= Html::encode($summary['socio_nombre']) ?></td>
                            <td class="text-center"><?= Html::encode($summary['cantidad']) ?></td>
                            <td class="text-right">RD$ <?= number_format($summary['precio_adquisicion_total'], 2) ?></td>
                            <td class="text-right">RD$ <?= number_format($summary['precio_venta_recomendado_total'], 2) ?></td>
                            <td class="text-right text-success">RD$ <?= number_format($summary['ganancia_neta_total'], 2) ?></td>
                            <td class="text-right text-warning">RD$ <?= number_format($summary['ganancia_socio_total'], 2) ?> (<?= $summary['porcentaje_socio'] ?>%)</td>
                            <td class="text-right text-info">RD$ <?= number_format($summary['ganancia_empresa_total'], 2) ?> (<?= $summary['porcentaje_empresa'] ?>%)</td>
                            <td class="text-center">
                                <?= Html::a('<i class="fa fa-trash"></i>', ['delete-in-draft', 'batch_id' => $summary['batch_id']], [
                                    'class' => 'btn btn-danger btn-xs',
                                    'data' => [
                                        'confirm' => '¿Estás seguro de que quieres eliminar este grupo de teléfonos en borrador?',
                                        'method' => 'post',
                                    ],
                                ]) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <?php if (!empty($inDraftSummary)): ?>
                <tfoot>
                    <tr style="font-weight: bold;">
                       <th colspan="3" class="text-right">Totales</th>
                       <th class="text-center"><?= Html::encode($total_cantidad) ?></th>
                       <th class="text-right">RD$ <?= number_format($total_precio_adquisicion, 2) ?></th>
                       <th class="text-right">RD$ <?= number_format($total_precio_venta_recomendado, 2) ?></th>
                       <th class="text-right text-success">RD$ <?= number_format($total_ganancia_neta, 2) ?></th>
                       <th class="text-right text-warning">RD$ <?= number_format($total_ganancia_socio, 2) ?></th>
                       <th class="text-right text-info">RD$ <?= number_format($total_ganancia_empresa, 2) ?></th>
                       <th></th>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>

<?php
Modal::begin([
    'header' => '<h4>Confirmar Suplidor</h4>',
    'id' => 'suplidor-modal',
    'footer' => '<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                 <button type="button" class="btn btn-primary" id="confirm-move">Confirmar</button>',
]);

echo Html::beginForm(['move-to-inventory'], 'post', ['id' => 'move-form']);
echo '<div class="form-group">';
echo Html::label('Nombre del Suplidor', 'suplidor-input', ['class' => 'control-label']);
echo Html::textInput('suplidor', '', ['id' => 'suplidor-input', 'class' => 'form-control', 'required' => true]);
echo '</div>';
echo Html::endForm();

Modal::end();

$this->registerJs("
    $('#confirm-move').on('click', function() {
        if ($('#suplidor-input').val()) {
            $('#move-form').submit();
        } else {
            alert('Debe proporcionar un nombre de suplidor.');
        }
    });
");
?>