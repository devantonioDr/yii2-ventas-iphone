<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\telefono\TelefonoMarcaModelo */
/* @var $marcas array */
/* @var $modelos array */

// Preparar lista de marcas para el dropdown
$marcasList = [];
if (!empty($marcas)) {
    $marcasList = ArrayHelper::map($marcas, 'marca', 'marca');
}

// Preparar lista de modelos para el dropdown
$modelosList = [];
if (!empty($modelos)) {
    $modelosList = ArrayHelper::map($modelos, 'modelo', 'modelo');
}

// URL para AJAX
$getModelosUrl = Url::to(['get-modelos-by-marca']);

$js = <<<JS
$(function() {
    var marcaInput = $('#telefonomarcamodelo-marca');
    var modeloInput = $('#telefonomarcamodelo-modelo');
    var modeloDatalist = $('#modelo-datalist');

    function cargarModelos(marca) {
        var m = (marca || '').trim();
        modeloDatalist.empty();
        if (!m) return;

        $.ajax({
            url: '{$getModelosUrl}',
            data: { marca: m },
            type: 'GET',
            dataType: 'json',
            success: function(resp) {
                modeloDatalist.empty();
                if (resp && resp.success && Array.isArray(resp.modelos)) {
                    resp.modelos.forEach(function(row) {
                        var value = (row && row.modelo) ? row.modelo : '';
                        if (value) {
                            var safe = $('<div>').text(value).html();
                            modeloDatalist.append('<option value="' + safe + '"></option>');
                        }
                    });
                }
            },
            error: function() {
                modeloDatalist.empty();
            }
        });
    }

    // Disparar carga al cambiar/escribir la marca
    marcaInput.on('change keyup', function() {
        cargarModelos($(this).val());
    });

    // Cargar al iniciar si hay marca ya cargada
    if (marcaInput.val()) {
        cargarModelos(marcaInput.val());
    }
});
JS;

$this->registerJs($js);
?>

<?php $form = ActiveForm::begin([
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"col-sm-9\">{input}\n{hint}\n{error}</div>",
        'labelOptions' => ['class' => 'col-sm-3 control-label'],
    ],
]); ?>

<div class="row">
    <div class="col-md-6">
        <?= $form->field($model, 'marca')->textInput([
            'id' => 'telefonomarcamodelo-marca',
            'class' => 'form-control',
            'list' => 'marcas-datalist',
            'placeholder' => 'Escriba o elija una marca...',
            'autocomplete' => 'off',
        ])->hint('Puede escribir una nueva marca o seleccionar una existente') ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'modelo')->textInput([
            'id' => 'telefonomarcamodelo-modelo',
            'class' => 'form-control',
            'list' => 'modelo-datalist',
            'placeholder' => 'Escriba o elija un modelo...',
            'autocomplete' => 'off',
        ])->hint('Las sugerencias se actualizan según la marca; también puede escribir uno nuevo') ?>
    </div>
</div>

<!-- Datalists para sugerencias -->
<datalist id="marcas-datalist">
<?php if (!empty($marcasList)): ?>
<?php foreach ($marcasList as $m): ?>
    <option value="<?= Html::encode($m) ?>"></option>
<?php endforeach; ?>
<?php endif; ?>
</datalist>

<datalist id="modelo-datalist">
<?php if (!empty($modelosList)): ?>
<?php foreach ($modelosList as $mo): ?>
    <option value="<?= Html::encode($mo) ?>"></option>
<?php endforeach; ?>
<?php endif; ?>
</datalist>

<div class="row">
    <div class="col-xs-12">
        <div class="alert alert-info">
            <h4><i class="fa fa-info-circle"></i> Cómo usar este formulario</h4>
            <ul>
                <li><strong>Marca:</strong> Seleccione de la lista o escriba directamente en el campo.</li>
                <li><strong>Modelo:</strong> Se cargarán automáticamente según la marca. También puede escribir directamente.</li>
                <li><strong>Nuevos valores:</strong> Si escribe marca o modelo que no existen, se crearán automáticamente.</li>
                <li><strong>ID único:</strong> El sistema generará un ID único basado en marca + modelo.</li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-9">
                <?= Html::submitButton(
                    $model->isNewRecord
                        ? '<i class="fa fa-save"></i> Crear Marca y Modelo'
                        : '<i class="fa fa-save"></i> Guardar Cambios',
                    [
                        'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
                    ]
                ) ?>
                <?= Html::a('<i class="fa fa-times"></i> Cancelar', ['index'], [
                    'class' => 'btn btn-default'
                ]) ?>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>

<script>
// Hacer que los selects permitan escribir
document.addEventListener('DOMContentLoaded', function() {
    var marcaSelect = document.getElementById('telefonomarcamodelo-marca');
    var modeloSelect = document.getElementById('telefonomarcamodelo-modelo');
    
    // Función para hacer un select editable
    function makeSelectEditable(select) {
        select.addEventListener('keypress', function(e) {
            if (e.key.length === 1) { // Si es un carácter
                var newValue = this.value + e.key;
                
                // Buscar si ya existe la opción
                var existingOption = Array.from(this.options).find(opt => opt.value === newValue);
                
                if (!existingOption) {
                    // Crear nueva opción
                    var newOption = document.createElement('option');
                    newOption.value = newValue;
                    newOption.text = newValue;
                    this.appendChild(newOption);
                }
                
                // Seleccionar la nueva opción
                this.value = newValue;
                
                // Trigger change event
                var event = new Event('change', { bubbles: true });
                this.dispatchEvent(event);
                
                e.preventDefault();
            }
        });
        
        select.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && this.value.length > 0) {
                var newValue = this.value.slice(0, -1);
                
                if (newValue === '') {
                    this.value = '';
                } else {
                    // Buscar o crear opción para el nuevo valor
                    var existingOption = Array.from(this.options).find(opt => opt.value === newValue);
                    
                    if (!existingOption) {
                        var newOption = document.createElement('option');
                        newOption.value = newValue;
                        newOption.text = newValue;
                        this.appendChild(newOption);
                    }
                    
                    this.value = newValue;
                }
                
                // Trigger change event
                var event = new Event('change', { bubbles: true });
                this.dispatchEvent(event);
                
                e.preventDefault();
            }
        });
    }
    
    // Aplicar a ambos selects
    makeSelectEditable(marcaSelect);
    makeSelectEditable(modeloSelect);
});
</script>

<style>
/* Permitir que los selects se vean como editables */
#telefonomarcamodelo-marca,
#telefonomarcamodelo-modelo {
    cursor: text;
}

#telefonomarcamodelo-marca:focus,
#telefonomarcamodelo-modelo:focus {
    outline: 2px solid #007bff;
    outline-offset: 2px;
}
</style>