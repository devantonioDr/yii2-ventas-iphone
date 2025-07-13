<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $gasto common\models\telefono\TelefonoGasto */
/* @var $telefono common\models\telefono\Telefono */

$this->title = 'Editar Gasto del Teléfono: ' . $telefono->imei;
$this->params['breadcrumbs'][] = ['label' => 'Teléfonos', 'url' => ['/telefono/index']];
$this->params['breadcrumbs'][] = ['label' => 'Editar', 'url' => ['/telefono/edit', 'id' => $telefono->id]];
$this->params['breadcrumbs'][] = ['label' => 'Gastos', 'url' => ['index', 'telefono_id' => $telefono->id]];
$this->params['breadcrumbs'][] = 'Editar Gasto';
?>

<div class="telefono-gasto-update">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-pencil"></i> Editar Gasto
                    </h3>
                    <div class="box-tools pull-right">
                        <?= Html::a('<i class="fa fa-arrow-left"></i> Volver', ['index', 'telefono_id' => $telefono->id], [
                            'class' => 'btn btn-default btn-sm'
                        ]) ?>
                    </div>
                </div>
                <div class="box-body">
                    <?= $this->render('_form', [
                        'gasto' => $gasto,
                        'telefono' => $telefono,
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>