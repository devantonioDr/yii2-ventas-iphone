<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use common\services\GananciaService;
use common\models\telefono\Telefono;

/* @var $this yii\web\View */
/* @var $searchModel common\models\telefono\TelefonoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Teléfonos';
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss("
@media (max-width: 767px) {
    .telefono-index .hidden-xs { display: none !important; }
    .telefono-index .visible-xs { display: block !important; }
    .telefono-index .btn { width: 100%; margin-bottom: 5px; }
    .telefono-index .box { margin-bottom: 10px; }
}
@media (min-width: 768px) {
    .telefono-index .visible-xs { display: none !important; }
}
.mobile-phone-card {
    margin-bottom: 10px;
}
");
?>

<div class="telefono-index">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-mobile"></i> Teléfonos</h3>
                    <div class="box-tools pull-right">
                        <?= Html::a('<i class="fa fa-upload"></i> <span class="hidden-xs">Insertar Lote</span>', ['batch-insert'], ['class' => 'btn btn-success btn-sm']) ?>
                    </div>
                </div>
                <div class="box-body">
                    <!-- Formulario de búsqueda -->
                    <div class="container-fluid">
                        <?php $form = ActiveForm::begin([
                            'action' => ['index'],
                            'method' => 'get',
                            'options' => ['class' => 'form-horizontal'],
                        ]); ?>
                        <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-3">
                                <?= $form->field($searchModel, 'imei')->textInput(['placeholder' => 'Buscar por IMEI...']) ?>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-2">
                                <?= $form->field($searchModel, 'marca')->dropDownList(
                                    ['' => 'Todas las marcas'] + \yii\helpers\ArrayHelper::map($marcas, 'marca', 'marca'),
                                    [
                                        'onchange' => '
                                            $.get("' . \yii\helpers\Url::to(['telefono/get-modelos-by-marca']) . '?marca=" + $(this).val(), function(data) {
                                                var select = $("#telefonosearch-modelo");
                                                select.html("<option value=\"\">Todos los modelos</option>");
                                                if(data.success) {
                                                    $.each(data.modelos, function(i, modelo) {
                                                        select.append("<option value=\"" + modelo.modelo + "\">" + modelo.modelo + "</option>");
                                                    });
                                                }
                                            });
                                        ',
                                    ]
                                ) ?>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-2">
                                <?= $form->field($searchModel, 'modelo')->dropDownList(
                                    ['' => 'Todos los modelos'] + \yii\helpers\ArrayHelper::map($modelos, 'modelo', 'modelo')
                                ) ?>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-2">
                                <?= $form->field($searchModel, 'status')->dropDownList(
                                    ['' => 'Todos'] + Telefono::getStatusOptions()
                                ) ?>
                            </div>
                            <div class="col-xs-12 col-md-3">
                                <div class="form-group">
                                    <label class="control-label">&nbsp;</label>
                                    <div>
                                        <?= Html::submitButton('<i class="fa fa-search"></i> <span class="hidden-xs">Buscar</span>', ['class' => 'btn btn-primary btn-block']) ?>
                                        <?= Html::a('<i class="fa fa-refresh"></i> <span class="hidden-xs">Limpiar</span>', ['index'], ['class' => 'btn btn-default btn-block']) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </div>

                    <!-- Tabla responsiva para todos los dispositivos -->
                    <div class="table-responsive">
                        <?= GridView::widget([
                            'dataProvider' => $dataProvider,
                            'filterModel' => null,
                            'columns' => [
                                ['class' => 'yii\grid\SerialColumn'],
                                [
                                    'attribute' => 'imei',
                                    'format' => 'raw',
                                    'value' => function ($model) {
                                        return \yii\helpers\Html::a(
                                            $model->imei,
                                            'javascript:void(0);',
                                            [
                                                'title' => 'Ver desglose de ganancia',
                                                'onclick' => "cargarDesglose({$model->id})",
                                                'style' => 'cursor:pointer; text-decoration: underline;',
                                                'class' => 'text-primary'
                                            ]
                                        );
                                    },
                                ],
                                'marca',
                                'modelo',
                                [
                                    'attribute' => 'status',
                                    'format' => 'raw',
                                    'value' => function ($model) {
                                        if ($model->status === Telefono::STATUS_EN_TIENDA) {
                                            return Html::tag('span', 'En Tienda', ['class' => 'label label-success']);
                                        } elseif ($model->status === Telefono::STATUS_VENDIDO) {
                                            return Html::tag('span', 'Vendido', ['class' => 'label label-warning']);
                                        }
                                        return Html::tag('span', ucfirst($model->status), ['class' => 'label label-default']);
                                    },
                                    'contentOptions' => ['class' => 'text-center'],
                                ],
                                [
                                    'attribute' => 'socio_id',
                                    'label' => 'Socio',
                                    'value' => function ($model) {
                                        return $model->socio ? $model->socio->nombre : 'Sin socio';
                                    },
                                    'contentOptions' => ['class' => 'text-center'],
                                ],
                                [
                                    'attribute' => 'precio_adquisicion',
                                    'label' => 'Adquisición RD$',
                                    'value' => function ($model) {
                                        return number_format($model->precio_adquisicion, 2);
                                    },
                                    'contentOptions' => ['class' => 'text-right'],
                                ],
                                [
                                    'label' => 'Gastos RD$',
                                    'value' => function ($model) {
                                        return number_format($model->getTotalGastos(), 2);
                                    },
                                    'contentOptions' => ['class' => 'text-right text-danger'],
                                ],
                                [
                                    'attribute' => 'precio_venta_recomendado',
                                    'label' => 'Venta RD$',
                                    'value' => function ($model) {
                                        return number_format($model->precio_venta_recomendado, 2);
                                    },
                                    'contentOptions' => ['class' => 'text-right'],
                                ],
                                [
                                    'attribute' => 'ganancia_absoluta',
                                    'label' => 'Ganancia Neta RD$',
                                    'value' => function ($model) {
                                        $service = new GananciaService();
                                        return number_format($service->calcular($model)->neta, 2);
                                    },
                                    'contentOptions' => ['class' => 'text-right text-success'],
                                ],
                                [
                                    'attribute' => 'ganancia_socio',
                                    'label' => 'Gan. Socio RD$',
                                    'value' => function ($model) {
                                        $service = new GananciaService();
                                        $ganancia = $service->calcular($model);
                                        return number_format($ganancia->socio, 2) . ' (' . $ganancia->porcentajeSocio . '%)';
                                    },
                                    'contentOptions' => ['class' => 'text-right text-warning'],
                                ],
                                [
                                    'attribute' => 'ganancia_empresa',
                                    'label' => 'Gan. Empresa RD$',
                                    'value' => function ($model) {
                                        $service = new GananciaService();
                                        $ganancia = $service->calcular($model);
                                        return number_format($ganancia->empresa, 2) . ' (' . $ganancia->porcentajeEmpresa . '%)';
                                    },
                                    'contentOptions' => ['class' => 'text-right text-info'],
                                ],
                                [
                                    'attribute' => 'fecha_ingreso',
                                    'format' => 'date',
                                    'contentOptions' => ['class' => 'text-center'],
                                ],
                                [
                                    'class' => 'yii\grid\ActionColumn',
                                    'template' => '{edit} {gastos}',
                                    'buttons' => [
                                        'edit' => function ($url, $model, $key) {
                                            return Html::a('<i class="fa fa-pencil"></i>', ['edit', 'id' => $model->id], [
                                                'class' => 'btn btn-xs btn-primary',
                                                'title' => 'Editar Teléfono',
                                            ]);
                                        },
                                        'gastos' => function ($url, $model, $key) {
                                            return Html::a('<i class="fa fa-money"></i>', ['telefono-gasto/index', 'telefono_id' => $model->id], [
                                                'class' => 'btn btn-xs btn-info',
                                                'title' => 'Ver/Añadir Gastos',
                                            ]);
                                        },
                                    ],
                                ],
                            ],
                            'tableOptions' => ['class' => 'table table-striped table-bordered'],
                            'summary' => 'Mostrando <b>{begin}-{end}</b> de <b>{totalCount}</b> teléfonos.',
                            'emptyText' => 'No se encontraron teléfonos.',
                        ]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->render('_desglose-modal') ?>