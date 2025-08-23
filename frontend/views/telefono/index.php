<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use common\models\telefono\Telefono;
use yii\widgets\Pjax;
use yii\helpers\Url;

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
    .telefono-index .js-mark-vendido { width: auto !important; display: inline-block; }
    .telefono-index .box { margin-bottom: 10px; }
}
@media (min-width: 768px) {
    .telefono-index .visible-xs { display: none !important; }
}
.mobile-phone-card {
    margin-bottom: 10px;
}
");
$this->registerJsFile('@web/js/imask.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerCssFile('https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css');
$this->registerJsFile('https://cdn.jsdelivr.net/npm/sweetalert2@11', ['depends' => [\yii\web\JqueryAsset::class]]);
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
                    <!-- Tabla responsiva para todos los dispositivos -->
                    <div class="table-responsive">
                        <?php Pjax::begin(['id' => 'telefono-grid-pjax']); ?>
                        <?= GridView::widget([
                            'dataProvider' => $dataProvider,
                            'filterModel' => $searchModel,
                            'columns' => [
                                ['class' => 'yii\\grid\\SerialColumn'],
                                [
                                    'attribute' => 'imei',
                                    'format' => 'raw',
                                    'value' => function ($model) {
                                        return Html::a($model->imei, ['edit', 'id' => $model->id]);
                                    }
                                ],
                                [
                                    'attribute' => 'marca',
                                    'filter' => ['' => 'Todas las marcas'] + \yii\helpers\ArrayHelper::map($marcas, 'marca', 'marca'),
                                    'headerOptions' => ['style' => 'min-width: 120px; white-space: nowrap;'],
                                    'contentOptions' => ['style' => 'min-width: 120px; white-space: nowrap;'],
                                ],
                                [
                                    'attribute' => 'modelo',
                                    'filter' => ['' => 'Todos los modelos'] + \yii\helpers\ArrayHelper::map($modelos, 'modelo', 'modelo'),
                                    'headerOptions' => ['style' => 'min-width: 120px; white-space: nowrap;'],
                                    'contentOptions' => ['style' => 'min-width: 120px; white-space: nowrap;'],
                                ],
                                [
                                    'attribute' => 'telefono_compra_id',
                                    'label' => 'Suplidor',
                                    'value' => function ($model) {
                                        return $model->telefonoCompra ? $model->telefonoCompra->suplidor : 'N/A';
                                    },
                                    'filter' => \yii\helpers\ArrayHelper::map(\common\models\telefono\TelefonoCompra::find()->select('suplidor')->distinct()->all(), 'suplidor', 'suplidor'),
                                ],
                                [
                                    'attribute' => 'status',
                                    'format' => 'raw',
                                    'value' => function ($model) {
                                        $statusLabels = [
                                            Telefono::STATUS_EN_TIENDA => ['class' => 'label-success', 'label' => 'En Tienda'],
                                            Telefono::STATUS_VENDIDO => ['class' => 'label-warning', 'label' => 'Vendido'],
                                            Telefono::STATUS_PAGADO => ['class' => 'label-info', 'label' => 'Pagado'],
                                            Telefono::STATUS_DEVUELTO => ['class' => 'label-danger', 'label' => 'Devuelto'],
                                            Telefono::STATUS_IN_DRAFT => ['class' => 'label-default', 'label' => 'En Borrador'],
                                        ];
                                        $status = $statusLabels[$model->status] ?? ['class' => 'label-default', 'label' => ucfirst($model->status)];
                                        $statusTag = Html::tag('span', $status['label'], ['class' => 'label ' . $status['class']]);
                                        $sellBtn = '';
                                        if ($model->status === Telefono::STATUS_EN_TIENDA) {
                                            $url = Url::to(['mark-as-vendido', 'id' => $model->id]);
                                            $previewUrl = Url::to(['preview-ganancia', 'id' => $model->id]);
                                            $modalUrl = Url::to(['sell-modal', 'id' => $model->id]);
                                            $sellBtn = Html::a('<i class="fa fa-check"></i>', 'javascript:void(0);', [
                                                'class' => 'btn btn-xs btn-warning js-mark-vendido',
                                                'title' => 'Marcar como vendido',
                                                'style' => 'margin-left:6px',
                                                'data-url' => $url,
                                                'data-preview-url' => $previewUrl,
                                                'data-modal-url' => $modalUrl,
                                                'data-imei' => $model->imei,
                                                'data-marca' => $model->marca,
                                                'data-modelo' => $model->modelo,
                                                'data-socio' => $model->socio ? $model->socio->nombre : 'Sin socio',
                                                'data-precio' => $model->precio_venta_recomendado,
                                            ]);
                                        }
                                        return $statusTag . ' ' . $sellBtn;
                                    },
                                    'filter' => ['' => 'Todos'] + Telefono::getStatusOptions(),
                                    'headerOptions' => ['style' => 'min-width: 120px; white-space: nowrap;'],
                                    'contentOptions' => ['class' => 'text-center', 'style' => 'min-width: 120px; white-space: nowrap;'],
                                ],
                                [
                                    'attribute' => 'socio_id',
                                    'label' => 'Socio',
                                    'value' => function ($model) {
                                        return $model->socio ? $model->socio->nombre : 'Sin socio';
                                    },
                                    'filter' => ['' => 'Todos los socios'] + \yii\helpers\ArrayHelper::map(\common\models\telefono\TelefonoSocio::find()->all(), 'id', 'nombre'),
                                    'contentOptions' => ['class' => 'text-center'],
                                ],
                                [
                                    'attribute' => 'precio_adquisicion',
                                    'label' => 'Adquisición RD$',
                                    'format' => 'raw',
                                    'value' => function ($model) {
                                        $url = Url::to(['update-precio-adquisicion', 'id' => $model->id]);
                                        $value = number_format($model->precio_adquisicion, 2);
                                        return Html::input('text', null, $value, [
                                            'class' => 'form-control input-sm js-precio-adq',
                                            'data-url' => $url,
                                            'style' => 'max-width:110px; text-align:right;',
                                            'aria-label' => 'Precio de adquisición',
                                        ]);
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
                                        return number_format(\common\services\telefono\GananciaService::calcular($model)['neta'], 2);
                                    },
                                    'contentOptions' => ['class' => 'text-right text-success'],
                                ],
                                [
                                    'attribute' => 'ganancia_empresa',
                                    'label' => 'Gan. Empresa RD$',
                                    'value' => function ($model) {
                                        $ganancia = \common\services\telefono\GananciaService::calcular($model);
                                        return number_format($ganancia['empresa'], 2) . ' (' . $ganancia['porcentajeEmpresa'] . '%)';
                                    },
                                    'contentOptions' => ['class' => 'text-right text-info'],
                                ],
                                [
                                    'attribute' => 'fecha_ingreso',
                                    'format' => 'date',
                                    'contentOptions' => ['class' => 'text-center'],
                                ],
                                [
                                    'class' => 'yii\\grid\\ActionColumn',
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
                        <?php Pjax::end(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<?= $this->render('_desglose-modal') ?>
<?= $this->render('_sell-modal') ?>

<?php
$this->registerJs(<<<'JS'
// IMask options for currency-like inputs
var adqMaskOptions = {
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

// Initialize IMask on focus to support PJAX reloads creating new inputs
$(document).on('focus', '.js-precio-adq', function(){
    var el = this;
    if (!el._mask && window.IMask) {
        el._mask = IMask(el, adqMaskOptions);
    }
});

// Submit on Enter key
$(document).on('keydown', '.js-precio-adq', function(e){
    if (e.key === 'Enter') {
        e.preventDefault();
        $(this).blur();
    }
});

// On blur, post value and reload grid to update gains via backend logic
$(document).on('blur', '.js-precio-adq', function(){
    var el = this;
    var $el = $(el);
    var url = $el.data('url');
    if (!url) return;
    var valor = null;
    if (el._mask) {
        valor = parseFloat(el._mask.unmaskedValue || '0');
    } else {
        var raw = ($el.val() || '').toString().trim();
        raw = raw.replace(/,/g, '').replace(',', '.');
        valor = parseFloat(raw);
    }
    if (!valor || valor <= 0) return;
    if (el._lastSent === valor) return;
    $el.prop('disabled', true);
    $.post(url, { precio_adquisicion: valor })
        .done(function(resp){
            if (resp && resp.success) {
                $.pjax.reload({container:'#telefono-grid-pjax'});
            } else {
                alert(resp && resp.message ? resp.message : 'No se pudo actualizar');
            }
        })
        .fail(function(){
            alert('Error al actualizar');
        })
        .always(function(){
            $el.prop('disabled', false);
            el._lastSent = valor;
        });
});
JS);
?>