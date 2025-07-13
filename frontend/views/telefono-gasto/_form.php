<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $gasto common\models\telefono\TelefonoGasto */
/* @var $telefono common\models\telefono\Telefono */
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
            'type' => 'number',
            'step' => '0.01',
            'min' => '0',
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