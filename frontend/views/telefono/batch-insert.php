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
                        <?= $form->field($model, 'marca')->textInput([
                            'id' => 'telefono-marca',
                            'list' => 'marcas-datalist',
                            'placeholder' => 'Escriba o elija una marca...',
                            'autocomplete' => 'off',
                        ]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'modelo')->textInput([
                            'id' => 'telefono-modelo',
                            'list' => 'modelos-datalist',
                            'placeholder' => 'Escriba o elija un modelo...',
                            'autocomplete' => 'off',
                        ]) ?>
                    </div>
                </div>

                <!-- Datalists de sugerencias -->
                <datalist id="marcas-datalist">
                <?php foreach (\yii\helpers\ArrayHelper::map($marcas, 'marca', 'marca') as $m): ?>
                    <option value="<?= Html::encode($m) ?>"></option>
                <?php endforeach; ?>
                </datalist>
                <datalist id="modelos-datalist">
                <?php foreach (\yii\helpers\ArrayHelper::map($modelos, 'modelo', 'modelo') as $mo): ?>
                    <option value="<?= Html::encode($mo) ?>"></option>
                <?php endforeach; ?>
                </datalist>

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
    // Sugerencias dinámicas para modelos según marca usando datalist
    var marcaInput = $('#telefono-marca');
    var modeloInput = $('#telefono-modelo');
    var modelosDatalist = $('#modelos-datalist');

    function cargarModelosDatalist(marca) {
        var m = (marca || '').trim();
        modelosDatalist.empty();
        if (!m) return;

        $.get('$modelos', { marca: m }, function(data) {
            modelosDatalist.empty();
            if (data && data.success && Array.isArray(data.modelos)) {
                data.modelos.forEach(function(row) {
                    var value = (row && row.modelo) ? row.modelo : '';
                    if (value) {
                        modelosDatalist.append('<option value="' + $('<div>').text(value).html() + '"></option>');
                    }
                });
            }
        });
    }

    marcaInput.on('change keyup', function() {
        cargarModelosDatalist($(this).val());
    });

    if (marcaInput.val()) {
        cargarModelosDatalist(marcaInput.val());
    }

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