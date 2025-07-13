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
    $('#telefono-marca').on('change', function() {
        var marca = $(this).val();
        if (marca) {
            $.get('" . \yii\helpers\Url::to(['telefono/get-modelos-by-marca']) . "?marca=' + marca, function(data) {
                var select = $('#telefono-modelo');
                select.html('<option value=\"\">Seleccione un modelo</option>');
                if (data.success) {
                    $.each(data.modelos, function(i, modelo) {
                        select.append('<option value=\"' + modelo.modelo + '\">' + modelo.modelo + '</option>');
                    });
                }
            });
        } else {
            $('#telefono-modelo').html('<option value=\"\">Seleccione un modelo</option>');
        }
    });

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

                    <?= $form->field($telefono, 'marca')->dropDownList(
                        ['' => 'Seleccione una marca'] + ArrayHelper::map($marcas, 'marca', 'marca'),
                        ['id' => 'telefono-marca']
                    ) ?>

                    <?= $form->field($telefono, 'modelo')->dropDownList(
                        ['' => 'Seleccione un modelo'] + ArrayHelper::map($modelos, 'modelo', 'modelo'),
                        ['id' => 'telefono-modelo']
                    ) ?>

                    <?= $form->field($telefono, 'imei')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'Ingrese el IMEI del teléfono'
                    ]) ?>

                    <?= $form->field($telefono, 'status')->dropDownList(
                        \common\models\telefono\Telefono::getStatusOptions()
                    ) ?>

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