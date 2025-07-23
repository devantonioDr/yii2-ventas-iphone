<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\telefono\Telefono */
/* @var $marcas array */
/* @var $modelos array */

$this->title = 'Inserción Masiva de Teléfonos';
$this->params['breadcrumbs'][] = ['label' => 'Teléfonos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile('@web/js/imask.js', ['depends' => [\yii\web\JqueryAsset::class]]);
?>

<?php if (!empty($inDraftSummary)): ?>
    <?= $this->render('_in-draft-summary', ['inDraftSummary' => $inDraftSummary]) ?>
<?php endif; ?>

<div class="telefono-batch-insert">
    <div class="box box-primary">
        <div class="box-body">
            <div class="telefono-form">
                <?php $form = ActiveForm::begin(); ?>

                <h4>Información del Dispositivo</h4>
                <hr style="margin-top: 10px; margin-bottom: 20px;">

                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'marca')->dropDownList(
                            \yii\helpers\ArrayHelper::map($marcas, 'marca', 'marca'),
                            [
                                'prompt' => 'Seleccione una marca',
                                'id' => 'marca-select'
                            ]
                        ) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'modelo')->dropDownList(
                            \yii\helpers\ArrayHelper::map($modelos, 'modelo', 'modelo'),
                            [
                                'prompt' => 'Seleccione un modelo',
                                'id' => 'modelo-select'
                            ]
                        ) ?>
                    </div>
                </div>

                <div class="form-group">
                    <?= $form->field($model, 'imeis_string')->textarea([
                        'rows' => 10,
                        'placeholder' => "Ingrese los IMEIs, uno por línea. Ejemplo:&#10;123456789012345&#10;987654321098765"
                    ])->hint('Cada IMEI debe tener exactamente 15 dígitos numéricos.') ?>
                </div>

                <br>

                <h4>Información de Compra y Socios</h4>
                <hr style="margin-top: 10px; margin-bottom: 20px;">

                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'socio_id')->dropDownList(
                            \yii\helpers\ArrayHelper::map($socios, 'id', 'nombre'),
                            ['prompt' => 'Seleccione un socio']
                        ) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'socio_porcentaje')->textInput([
                            'id' => 'socio-porcentaje',
                            'type' => 'number',
                            'step' => '0.01',
                            'min' => '0',
                            'max' => '100',
                            'placeholder' => '20.00'
                        ]) ?>
                    </div>
                </div>

                <br>

                <h4>Información Financiera</h4>
                <hr style="margin-top: 10px; margin-bottom: 20px;">

                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'precio_adquisicion')->textInput([
                            'placeholder' => '0.00'
                        ]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'precio_venta_recomendado')->textInput([
                            'placeholder' => '0.00'
                        ]) ?>
                    </div>
                </div>

                <br>

                <div class="form-group">
                    <?= Html::submitButton('Insertar Teléfonos', ['class' => 'btn btn-success']) ?>
                    <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-default']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>


</div>

<?php
$modelos = Url::to(['get-modelos-by-marca']);
$socioInfo = Url::to(['telefono-socio/get-socio-info']);
$script = <<<JS
$(document).ready(function() {
    // Código existente para marcas y modelos
    $('#marca-select').change(function() {
        var marca = $(this).val();
        var modeloSelect = $('#modelo-select');
        
        modeloSelect.html('<option value="">Cargando...</option>');
        
        if (marca) {
            $.get('$modelos', {marca: marca}, function(data) {
                if (data.success) {
                    modeloSelect.html('<option value="">Seleccione un modelo</option>');
                    $.each(data.modelos, function(index, modelo) {
                        modeloSelect.append('<option value="' + modelo.modelo + '">' + modelo.modelo + '</option>');
                    });
                } else {
                    modeloSelect.html('<option value="">Error al cargar modelos</option>');
                }
            });
        } else {
            modeloSelect.html('<option value="">Seleccione un modelo</option>');
        }
    });

    // Nuevo código para cargar información del socio
    $('#telefono-socio_id').change(function() {
        var socioId = $(this).val();
        var porcentajeField = $('#socio-porcentaje');
        
        if (socioId) {
            $.get('$socioInfo', {id: socioId}, function(data) {
                if (data.success) {
                    porcentajeField.val(data.socio.margen_ganancia);
                } else {
                    porcentajeField.val('');
                    console.error('Error al cargar información del socio:', data.message);
                }
            });
        } else {
            porcentajeField.val('');
        }
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
    var pa_mask = IMask(pa_element, maskOptions);
    var pvr_mask = IMask(pvr_element, maskOptions);

    $('form').on('submit', function() {
        $('#telefono-precio_adquisicion').val(pa_mask.unmaskedValue);
        $('#telefono-precio_venta_recomendado').val(pvr_mask.unmaskedValue);
        return true;
    });
});
JS;

$this->registerJs($script);
?>