<?php

use common\models\telefono\TelefonoSocioPago;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use common\models\telefono\TelefonoSocio;

/** @var yii\web\View $this */
/** @var common\models\telefono\TelefonoSocioPagoSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Pagos a Socios';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="telefono-socio-pago-index">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-money"></i> <?= Html::encode($this->title) ?></h3>
        </div>
        <div class="box-body">

            <?php
            /** @var \yii\db\ActiveQuery $query */
            $query = clone $dataProvider->query;
            $totalGananciaSocio = $query->sum('ganancia_socio') ?? 0;
            $totalGananciaEmpresa = $query->sum('ganancia_empresa') ?? 0;
            ?>

            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-aqua"><i class="fa fa-users"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Ganancia Socio</span>
                            <span class="info-box-number"><?= Yii::$app->formatter->asCurrency($totalGananciaSocio) ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-green"><i class="fa fa-building"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Ganancia Empresa</span>
                            <span class="info-box-number"><?= Yii::$app->formatter->asCurrency($totalGananciaEmpresa) ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],

                        'codigo_factura',
                        [
                            'attribute' => 'socio_id',
                            'value' => 'socio.nombre',
                            'label' => 'Socio',
                            'filter' => ['' => 'Todos los socios'] + \yii\helpers\ArrayHelper::map(common\models\telefono\TelefonoSocio::find()->all(), 'id', 'nombre'),
                        ],
                        'fecha_pago:datetime',
                        [
                            'attribute' => 'cantidad_telefonos',
                            'filter' => false,
                        ],
                        [
                            'attribute' => 'ganancia_socio',
                            'format' => 'currency',
                            'filter' => false,
                        ],
                        [
                            'attribute' => 'ganancia_empresa',
                            'format' => 'currency',
                            'filter' => false,
                        ],
                        [
                            'attribute' => 'ganancia_neta',
                            'format' => 'currency',
                            'filter' => false,
                        ],
                        [
                            'attribute' => 'gastos',
                            'format' => 'currency',
                            'filter' => false,
                        ],
                        [
                            'attribute' => 'invertido',
                            'format' => 'currency',
                            'filter' => false,
                        ],
                        [
                            'class' => ActionColumn::className(),
                             'template' => '{view} {revertir}',
                            'buttons' => [
                                'view' => function ($url, $model, $key) {
                                    return Html::a('<i class="fa fa-eye"></i>', ['view', 'id' => $model->id], [
                                        'class' => 'btn btn-xs btn-info',
                                        'title' => 'Ver Detalle',
                                    ]);
                                },
                                'revertir' => function ($url, $model, $key) {
                                    return Html::a('<i class="fa fa-undo"></i>', ['revertir', 'id' => $model->id], [
                                        'class' => 'btn btn-xs btn-danger',
                                        'title' => 'Revertir Pago',
                                        'data' => [
                                            'confirm' => '¿Está seguro de que desea revertir este pago? Esta acción no se puede deshacer.',
                                            'method' => 'post',
                                        ],
                                    ]);
                                },
                            ],
                        ],
                    ],
                    'tableOptions' => ['class' => 'table table-striped table-bordered'],
                    'summary' => 'Mostrando <b>{begin}-{end}</b> de <b>{totalCount}</b> pagos.',
                    'emptyText' => 'No se encontraron pagos.',
                ]); ?>
            </div>
        </div>
    </div>
</div> 