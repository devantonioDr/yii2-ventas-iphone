<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $telefono common\models\telefono\Telefono */
/* @var $marcas array */
/* @var $modelos array */
/* @var $socios array */
/* @var $gastos array */

$this->title = 'Editar Teléfono - ' . $telefono->imei;
$this->params['breadcrumbs'][] = ['label' => 'Teléfonos', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Editar';

$this->registerJsFile('@web/js/imask.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJs("
$(document).ready(function() {
    // Sugerencias dinámicas de modelos usando datalist según la marca
    var modelosDatalist = $('#modelos-datalist');
    function cargarModelosDatalist(marca) {
        var m = (marca || '').trim();
        modelosDatalist.empty();
        if (!m) return;
        $.get('" . \yii\helpers\Url::to(['telefono/get-modelos-by-marca']) . "', { marca: m }, function(data) {
            modelosDatalist.empty();
            if (data && data.success && Array.isArray(data.modelos)) {
                $.each(data.modelos, function(i, row) {
                    var value = (row && row.modelo) ? row.modelo : '';
                    if (value) {
                        modelosDatalist.append('<option value=\"' + $('<div>').text(value).html() + '\"></option>');
                    }
                });
            }
        });
    }
    $('#telefono-marca').on('change keyup', function() {
        cargarModelosDatalist($(this).val());
    });
    if ($('#telefono-marca').val()) {
        cargarModelosDatalist($('#telefono-marca').val());
    }

    var socioSelect = $('#telefono-socio_id');
    var porcentajeField = $('.field-telefono-socio_porcentaje');

    function togglePorcentajeField() {
        if (socioSelect.val()) {
            porcentajeField.slideDown();
        } else {
            porcentajeField.slideUp();
            $('#telefono-socio_porcentaje').val('');
        }
    }

    togglePorcentajeField();

    socioSelect.on('change', function() {
        togglePorcentajeField();
    });

    // IMask for currency inputs
    var maskOptions = {
        mask: Number,
        scale: 2,
        signed: false,
        thousandsSeparator: ',',
        padFractionalZeros: true,
        normalizeZeros: true,
        radix: '.',
        mapToRadix: ['.'],
        min: 0
    };

    var pa_element = document.getElementById('telefono-precio_adquisicion');
    var pvr_element = document.getElementById('telefono-precio_venta_recomendado');
    var pa_mask, pvr_mask;

    if (pa_element) {
        pa_mask = IMask(pa_element, maskOptions);
    }
    if (pvr_element) {
        pvr_mask = IMask(pvr_element, maskOptions);
    }

    // Autocalcular precio de venta recomendado
    if (pa_mask) {
        pa_mask.on('accept', function() {
            if (pvr_mask) {
                var adquisicionValue = parseFloat(pa_mask.unmaskedValue);
                if (!isNaN(adquisicionValue) && adquisicionValue > 0) {
                    var ventaRecomendada = adquisicionValue * 1.15;
                    pvr_mask.value = ventaRecomendada.toFixed(2);
                } else {
                    pvr_mask.value = '';
                }
            }
        });
    }

    $('form').on('submit', function() {
        if (pa_element) {
            pa_mask.unmaskedValue = pa_mask.value;
            $('#telefono-precio_adquisicion').val(pa_mask.unmaskedValue);
        }
        if (pvr_element) {
            pvr_mask.unmaskedValue = pvr_mask.value;
            $('#telefono-precio_venta_recomendado').val(pvr_mask.unmaskedValue);
        }
        return true;
    });
});
");
?>

<div class="telefono-edit">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-solid" style="margin-bottom: 20px;">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-calculator"></i> Desglose Financiero Actual</h3>
                </div>
                <div class="box-body">
                    <?= $this->render('_desglose-content', [
                        'telefono' => $telefono,
                        'gastos' => $gastos,
                    ]) ?>
                </div>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-edit"></i> Editar Teléfono
                    </h3>
                    <div class="box-tools pull-right">
                        <?= Html::a('<i class="fa fa-money"></i> Gastos', ['telefono-gasto/index', 'telefono_id' => $telefono->id], [
                            'class' => 'btn btn-info btn-sm',
                            'title' => 'Ver gastos del teléfono'
                        ]) ?>
                        <?= Html::a('<i class="fa fa-arrow-left"></i> Volver', ['index'], ['class' => 'btn btn-default btn-sm']) ?>
                    </div>
                </div>
                <div class="box-body">
                    <?php $form = ActiveForm::begin([
                        'options' => ['class' => 'form-horizontal'],
                        'fieldConfig' => [
                            'template' => "{label}\n<div class=\"col-sm-9\">{input}\n{hint}\n{error}</div>",
                            'labelOptions' => ['class' => 'col-sm-3 control-label'],
                        ],
                    ]); ?>

                    <h4 class="page-header" style="margin-top: 0;">Información del Teléfono</h4>

                    <?= $form->field($telefono, 'marca')->textInput([
                        'id' => 'telefono-marca',
                        'list' => 'marcas-datalist',
                        'placeholder' => 'Escriba o elija una marca...',
                        'autocomplete' => 'off',
                    ]) ?>

                    <?= $form->field($telefono, 'modelo')->textInput([
                        'id' => 'telefono-modelo',
                        'list' => 'modelos-datalist',
                        'placeholder' => 'Escriba o elija un modelo...',
                        'autocomplete' => 'off',
                    ]) ?>

                    <?= $form->field($telefono, 'imei')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'Ingrese el IMEI del teléfono'
                    ]) ?>

                    <?php
                    $statusOptions = \common\models\telefono\Telefono::getStatusOptions();
                    unset($statusOptions[\common\models\telefono\Telefono::STATUS_PAGADO]);
                    ?>
                    <?= $form->field($telefono, 'status')->dropDownList($statusOptions) ?>

                    <h4 class="page-header">Información Financiera</h4>

                    <?= $form->field($telefono, 'precio_adquisicion')->textInput([
                        'placeholder' => '0.00'
                    ]) ?>

                    <?= $form->field($telefono, 'precio_venta_recomendado')->textInput([
                        'placeholder' => '0.00'
                    ]) ?>

                    <h4 class="page-header">Información del Socio</h4>

                    <?= $form->field($telefono, 'socio_id')->dropDownList(
                        ['' => 'Sin socio'] + ArrayHelper::map($socios, 'id', 'nombre')
                    ) ?>

                    <?= $form->field($telefono, 'socio_porcentaje')->textInput([
                        'type' => 'number',
                        'step' => 1,
                        'min' => 0,
                        'max' => 100,
                        'placeholder' => 'Ej: 50'
                    ])->hint('Porcentaje de la ganancia que corresponde al socio.') ?>

                </div>
                <!-- Datalists para sugerencias de marca y modelo -->
                <datalist id="marcas-datalist">
                <?php foreach (ArrayHelper::map($marcas, 'marca', 'marca') as $m): ?>
                    <option value="<?= Html::encode($m) ?>"></option>
                <?php endforeach; ?>
                </datalist>
                <datalist id="modelos-datalist">
                <?php foreach (ArrayHelper::map($modelos, 'modelo', 'modelo') as $mo): ?>
                    <option value="<?= Html::encode($mo) ?>"></option>
                <?php endforeach; ?>
                </datalist>
                <div class="box-footer">
                    <div class="form-group" style="margin-bottom: 0;">
                        <div class="col-sm-offset-3 col-sm-9">
                            <?= Html::submitButton('<i class="fa fa-save"></i> Guardar Cambios', [
                                'class' => 'btn btn-primary'
                            ]) ?>
                            <?= Html::a('<i class="fa fa-times"></i> Cancelar', ['index'], [
                                'class' => 'btn btn-default'
                            ]) ?>
                        </div>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>

    </div>
</div>