<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $gasto common\models\telefono\TelefonoGasto */
/* @var $telefono common\models\telefono\Telefono */

$this->registerJsFile('@web/js/imask.js', ['depends' => [\yii\web\JqueryAsset::class]]);

$inputId = Html::getInputId($gasto, 'monto_gasto');
$js = <<<JS
var element = document.getElementById('{$inputId}');
var maskOptions = {
    mask: Number,
    scale: 2,
    signed: false,
    thousandsSeparator: ',',
    padFractionalZeros: true,
    normalizeZeros: true,
    radix: '.',
    mapToRadix: ['.'],
};
var mask = IMask(element, maskOptions);
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
        <?= $form->field($gasto, 'descripcion')->textInput([
            'maxlength' => true,
            'placeholder' => 'Ej: Reparación de pantalla, cambio de batería, etc.'
        ]) ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($gasto, 'monto_gasto')->textInput([
            'type' => 'text',
            'placeholder' => '0.00'
        ]) ?>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-9">
                <?= Html::submitButton(
                    $gasto->isNewRecord
                        ? '<i class="fa fa-save"></i> Guardar Gasto'
                        : '<i class="fa fa-save"></i> Guardar Cambios',
                    [
                        'class' => $gasto->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
                    ]
                ) ?>
                <?= Html::a('<i class="fa fa-times"></i> Cancelar', ['index', 'telefono_id' => $telefono->id], [
                    'class' => 'btn btn-default'
                ]) ?>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?> 