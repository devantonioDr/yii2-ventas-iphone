<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\telefono\TelefonoSocio */

$this->title = 'Crear Nuevo Socio';
$this->params['breadcrumbs'][] = ['label' => 'Socios de Teléfonos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="telefono-socio-create">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Información del Socio</h3>
        </div>

        <div class="box-body">
            <?php $form = ActiveForm::begin([
                'options' => ['class' => 'form-horizontal'],
                'fieldConfig' => [
                    'template' => "{label}\n<div class=\"col-sm-8\">{input}\n{hint}\n{error}</div>",
                    'labelOptions' => ['class' => 'col-sm-4 control-label'],
                ],
            ]); ?>

            <?= $form->field($model, 'nombre')->textInput([
                'maxlength' => true,
                'placeholder' => 'Ingrese el nombre del socio',
                'class' => 'form-control'
            ]) ?>


            <?= $form->field($model, 'margen_ganancia')->textInput([
                'type' => 'number',
                'step' => '0.01',
                'min' => '0',
                'max' => '999.99',
                'placeholder' => '0.00',
                'class' => 'form-control'
            ])->hint('Porcentaje de ganancia (ej: 15.50 para 15.5%)') ?>



            <div class="col-sm-offset-4 col-sm-8">
                <?= Html::submitButton('<i class="fa fa-save"></i> Crear Socio', [
                    'class' => 'btn btn-success btn-flat'
                ]) ?>
                <?= Html::a('<i class="fa fa-arrow-left"></i> Cancelar', ['index'], [
                    'class' => 'btn btn-default btn-flat'
                ]) ?>
            </div>


            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>