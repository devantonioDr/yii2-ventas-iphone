<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\telefono\TelefonoSocio $model */
/** @var yii\widgets\ActiveForm $form */
/** @var array $ganancia */

$this->title = 'Actualizar Socio: ' . $model->nombre;
$this->params['breadcrumbs'][] = ['label' => 'Socios', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="row">
    <div class="col-md-6">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Datos del Socio</h3>
            </div>
            <?php $form = ActiveForm::begin(); ?>
            <div class="box-body">
                <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'margen_ganancia')->textInput() ?>
            </div>
            <div class="box-footer">
                <?= Html::submitButton('Guardar', ['class' => 'btn btn-success btn-flat']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
    <div class="col-md-6">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Desglose de Ganancias Pendientes</h3>
            </div>
            <div class="box-body">
                <?= $this->render('_desglose', [
                    'ganancia' => $ganancia,
                ]) ?>
            </div>
        </div>
    </div>
</div> 