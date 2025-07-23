<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\telefono\TelefonoSocioWalletTransaction */

$this->title = 'Detalle de Transacción #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Socios', 'url' => ['telefono-socio/index']];
$this->params['breadcrumbs'][] = ['label' => 'Wallet de ' . $model->wallet->telefonoSocio->nombre, 'url' => ['wallet/index', 'socio_id' => $model->wallet->telefonoSocio->id]];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="telefono-socio-wallet-transaction-view">

    <div class="row">
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Detalles de la Transacción</h3>
                </div>
                <div class="box-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
                            [
                                'label' => 'Socio',
                                'value' => $model->wallet->telefonoSocio->nombre,
                            ],
                            [
                                'attribute' => 'type',
                                'label' => 'Tipo',
                                'value' => function ($model) {
                                    if ($model->type === 'credit') {
                                        return '<span class="label label-success">Crédito</span>';
                                    } else {
                                        return '<span class="label label-danger">Débito</span>';
                                    }
                                },
                                'format' => 'raw',
                            ],
                            'amount:currency',
                            'current_balance:currency',
                            'comment:ntext',
                            [
                                'attribute' => 'created_at',
                                'label' => 'Fecha de Transacción',
                                'format' => 'datetime',
                            ],
                            [
                                'attribute' => 'updated_at',
                                'label' => 'Última Actualización',
                                'format' => 'datetime',
                            ],
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">Fotos</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <?php if (!empty($model->photos)): ?>
                            <?php foreach ($model->photos as $photo): ?>
                                <div class="col-md-4">
                                    <a href="<?= Yii::getAlias('@web') . '/' . $photo->path ?>" target="_blank">
                                        <?= Html::img(Yii::getAlias('@web') . '/' . $photo->path, ['class' => 'img-responsive img-thumbnail', 'style' => 'margin-bottom: 15px;']) ?>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-md-12">
                                <p>No hay fotos para esta transacción.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 