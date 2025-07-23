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
                    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'class' => 'pull-right']]); ?>
                    
                    <div class="form-group">
                        <?= Html::label('Adjuntar Fotos de Depósito', 'photos', ['class' => 'control-label']) ?>
                        <?= Html::fileInput('photos[]', null, ['class' => 'form-control', 'multiple' => true, 'id' => 'pago-photos']) ?>
                    </div>
                    <div id="preview-container" class="row"></div>

                    <?= Html::submitButton('<i class="fa fa-credit-card"></i> Confirmar y Realizar Pago', ['class' => 'btn btn-success btn-lg']) ?>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs(<<<JS
    let selectedFiles = [];

    document.getElementById('pago-photos').addEventListener('change', function(event) {
        const files = event.target.files;
        for (const file of files) {
            selectedFiles.push(file);
        }
        renderPreviews();
    });

    function renderPreviews() {
        const previewContainer = document.getElementById('preview-container');
        previewContainer.innerHTML = '';
        selectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = `
                    <div class="col-md-3" style="margin-bottom: 10px;">
                        <img src="\${e.target.result}" class="img-responsive" style="width:100%; height:auto;"/>
                        <button type="button" class="btn btn-danger btn-xs remove-photo" data-index="\${index}" style="position:absolute; top:5px; right:15px;">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                `;
                previewContainer.innerHTML += preview;
            }
            reader.readAsDataURL(file);
        });
        updateFileInput();
    }

    document.getElementById('preview-container').addEventListener('click', function(event) {
        if (event.target.classList.contains('remove-photo') || event.target.parentElement.classList.contains('remove-photo')) {
            const button = event.target.closest('.remove-photo');
            const index = button.getAttribute('data-index');
            selectedFiles.splice(index, 1);
            renderPreviews();
        }
    });

    function updateFileInput() {
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => {
            dataTransfer.items.add(file);
        });
        document.getElementById('pago-photos').files = dataTransfer.files;
    }

    document.querySelector('form').addEventListener('submit', function(event) {
        if (selectedFiles.length === 0) {
            event.preventDefault();
            alert('Debe adjuntar al menos una foto del comprobante de pago.');
        }
    });
JS
,
    \yii\web\View::POS_END
);
?>