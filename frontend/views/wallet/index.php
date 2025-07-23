<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $socio common\models\telefono\TelefonoSocio */
/* @var $wallet common\models\telefono\TelefonoSocioWallet */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->registerJsFile('@web/js/imask.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->title = 'Wallet de ' . $socio->nombre;
$this->params['breadcrumbs'][] = ['label' => 'Socios', 'url' => ['telefono-socio/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="wallet-index">

    <div class="row">
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Balance</h3>
                </div>
                <div class="box-body">
                    <h2 id="wallet-balance"><?= Yii::$app->formatter->asCurrency($wallet ? $wallet->balance : 0) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="box box-solid">
                <div class="box-body text-center">
                    <?= Html::button('Acreditar', ['class' => 'btn btn-success', 'data-toggle' => 'modal', 'data-target' => '#credit-modal']) ?>
                    <?= Html::button('Debitar', ['class' => 'btn btn-danger', 'data-toggle' => 'modal', 'data-target' => '#debit-modal']) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Transacciones</h3>
        </div>
        <div class="box-body" id="transaction-grid">
            <?php \yii\widgets\Pjax::begin(['id' => 'transactions-pjax']); ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'rowOptions' => function ($model, $key, $index, $grid) {
                    if ($model->type === 'credit') {
                        return ['class' => 'success'];
                    }
                    return ['class' => 'danger'];
                },
                'columns' => [
                    [
                        'attribute' => 'id',
                        'contentOptions' => ['style' => 'vertical-align: middle;']
                    ],
                    [
                        'attribute' => 'type',
                        'format' => 'html',
                        'value' => function($model) {
                            if ($model->type === 'credit') {
                                return '<span class="label label-success">Crédito</span>';
                            } else {
                                return '<span class="label label-danger">Débito</span>';
                            }
                        },
                        'headerOptions' => ['style' => 'text-align: center;'],
                        'contentOptions' => ['style' => 'text-align: center; vertical-align: middle;'],
                    ],
                    [
                        'attribute' => 'amount',
                        'format' => 'html',
                        'value' => function ($model) {
                            $sign = $model->type === 'credit' ? '+' : '-';
                            return '<b>' . $sign . ' ' . Yii::$app->formatter->asCurrency($model->amount) . '</b>';
                        },
                        'headerOptions' => ['style' => 'text-align: right;'],
                        'contentOptions' => function ($model, $key, $index, $column) {
                            $class = $model->type === 'credit' ? 'text-success' : 'text-danger';
                            return ['class' => $class, 'style' => 'text-align: right; vertical-align: middle; font-size: 1.1em;'];
                        }
                    ],
                    [
                        'attribute' => 'current_balance',
                        'format' => 'currency',
                        'headerOptions' => ['style' => 'text-align: right;'],
                        'contentOptions' => ['style' => 'text-align: right; vertical-align: middle;'],
                    ],
                    [
                        'attribute' => 'comment',
                        'format' => 'ntext',
                        'contentOptions' => ['style' => 'vertical-align: middle;']
                    ],
                    [
                        'attribute' => 'created_at',
                        'format' => 'datetime',
                        'contentOptions' => ['style' => 'vertical-align: middle;']
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view}',
                        'buttons' => [
                            'view' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['wallet/view', 'id' => $model->id], [
                                    'title' => 'Ver',
                                ]);
                            },
                        ],
                        'contentOptions' => ['style' => 'text-align: center; vertical-align: middle;']
                    ],
                ],
            ]); ?>
            <?php \yii\widgets\Pjax::end(); ?>
        </div>
    </div>
</div>

<?php
Modal::begin([
    'header' => '<h2 class="text-success">Acreditar Saldo</h2>',
    'id' => 'credit-modal',
    'footer' => Html::submitButton('Acreditar', ['class' => 'btn btn-success', 'form' => 'credit-form']),
]);
?>
<form id="credit-form" action="<?= Url::to(['wallet/credit', 'socio_id' => $socio->id]) ?>" method="post" enctype="multipart/form-data">
    <div class="form-group">
        <?= Html::label('Monto', 'credit-amount', ['class' => 'control-label']) ?>
        <?= Html::textInput('amount', null, ['id' => 'credit-amount', 'class' => 'form-control', 'required' => true]) ?>
    </div>
    <div class="form-group">
        <?= Html::label('Comentario', 'credit-comment', ['class' => 'control-label']) ?>
        <?= Html::textarea('comment', null, ['id' => 'credit-comment', 'class' => 'form-control', 'rows' => 2, 'required' => true]) ?>
    </div>
    <div class="form-group">
        <?= Html::label('Foto', 'credit-photo', ['class' => 'control-label']) ?>
        <?= Html::fileInput('photos[]', null, ['id' => 'credit-photo', 'class' => 'form-control', 'multiple' => true, 'accept' => 'image/*']) ?>
        <div class="image-preview" id="credit-image-preview" style="margin-top: 10px;"></div>
    </div>
</form>
<?php Modal::end(); ?>

<?php
Modal::begin([
    'header' => '<h2 class="text-danger">Debitar Saldo</h2>',
    'id' => 'debit-modal',
    'footer' => Html::submitButton('Debitar', ['class' => 'btn btn-danger', 'form' => 'debit-form']),
]);
?>
<form id="debit-form" action="<?= Url::to(['wallet/debit', 'socio_id' => $socio->id]) ?>" method="post" enctype="multipart/form-data">
    <div class="form-group">
        <?= Html::label('Monto', 'debit-amount', ['class' => 'control-label']) ?>
        <?= Html::textInput('amount', null, ['id' => 'debit-amount', 'class' => 'form-control', 'required' => true]) ?>
    </div>
    <div class="form-group">
        <?= Html::label('Comentario', 'debit-comment', ['class' => 'control-label']) ?>
        <?= Html::textarea('comment', null, ['id' => 'debit-comment', 'class' => 'form-control', 'rows' => 2, 'required' => true]) ?>
    </div>
    <div class="form-group">
        <?= Html::label('Foto', 'debit-photo', ['class' => 'control-label']) ?>
        <?= Html::fileInput('photos[]', null, ['id' => 'debit-photo', 'class' => 'form-control', 'multiple' => true, 'accept' => 'image/*']) ?>
        <div class="image-preview" id="debit-image-preview" style="margin-top: 10px;"></div>
    </div>
</form>
<?php Modal::end(); ?>

<?php
$walletBalanceUrl = Url::to(['wallet/get-balance', 'socio_id' => $socio->id]);
$script = <<< JS

let creditFiles = new DataTransfer();
let debitFiles = new DataTransfer();

var creditAmountInput = document.getElementById('credit-amount');
var debitAmountInput = document.getElementById('debit-amount');

var currencyMaskOptions = {
    mask: Number,
    scale: 2,
    signed: false,
    thousandsSeparator: ',',
    padFractionalZeros: false,
    normalizeZeros: true,
    radix: '.'
};

var creditAmountMask = IMask(creditAmountInput, currencyMaskOptions);
var debitAmountMask = IMask(debitAmountInput, currencyMaskOptions);

function handleFiles(files, previewContainerId, fileList) {
    let previewContainer = document.getElementById(previewContainerId);
    for (const file of files) {
        if (!file.type.startsWith('image/')){ continue }
        
        let reader = new FileReader();
        reader.onload = function(e) {
            let col = document.createElement('div');
            col.className = 'col-md-3 col-sm-4 col-xs-6';
            
            let imgContainer = document.createElement('div');
            imgContainer.className = 'img-thumbnail';
            imgContainer.style.position = 'relative';
            imgContainer.style.padding = '5px';

            let img = document.createElement('img');
            img.src = e.target.result;
            img.style.width = '100%';

            let removeBtn = document.createElement('button');
            removeBtn.innerHTML = '&times;';
            removeBtn.className = 'btn btn-danger btn-xs';
            removeBtn.style.position = 'absolute';
            removeBtn.style.top = '2px';
            removeBtn.style.right = '2px';
            
            removeBtn.onclick = function() {
                // Remove from view
                col.remove();
                // Remove from FileList
                let newFiles = new DataTransfer();
                for (let i = 0; i < fileList.files.length; i++) {
                    if (fileList.files[i].name !== file.name) {
                        newFiles.items.add(fileList.files[i]);
                    }
                }
                fileList.items.clear();
                for(let f of newFiles.files) {
                    fileList.items.add(f);
                }
            };
            
            imgContainer.appendChild(img);
            imgContainer.appendChild(removeBtn);
            col.appendChild(imgContainer);
            previewContainer.appendChild(col);
        }
        reader.readAsDataURL(file);
        fileList.items.add(file);
    }
}

document.getElementById('credit-photo').addEventListener('change', function(e) {
    handleFiles(this.files, 'credit-image-preview', creditFiles);
});

document.getElementById('debit-photo').addEventListener('change', function(e) {
    handleFiles(this.files, 'debit-image-preview', debitFiles);
});


function updateWalletBalance() {
    $.get('{$walletBalanceUrl}', function(data) {
        if(data.success) {
            $('#wallet-balance').text(data.balance);
        }
    });
}

$(function(){
    $('#credit-form').on('submit', function(e){
        e.preventDefault();
        var form = $(this);
        var url = form.attr('action');
        
        var amount = creditAmountMask.unmaskedValue;
        var comment = form.find('textarea[name="comment"]').val();

        if (!amount || parseFloat(amount) <= 0) {
            alert('El monto debe ser un número positivo.');
            return;
        }
        if (!comment) {
            alert('El comentario no puede estar vacío.');
            return;
        }

        var formData = new FormData();

        formData.append('amount', amount);
        formData.append('comment', comment);
        
        for (let i = 0; i < creditFiles.files.length; i++) {
            formData.append('photos[]', creditFiles.files[i]);
        }

        $.ajax({
            url: url,
            type: 'post',
            data: formData,
            contentType: false,
            processData: false,
            success: function(data){
                if(data.success){
                    $('#credit-modal').modal('hide');
                    $.pjax.reload({container:'#transactions-pjax'});
                    updateWalletBalance();
                    // Clear form
                    $('#credit-form')[0].reset();
                    $('#credit-image-preview').empty();
                    creditFiles.items.clear();
                } else {
                    alert(data.message);
                }
            },
            error: function(jqXHR){
                alert(jqXHR.responseJSON.message);
            }
        });
    });

    $('#debit-form').on('submit', function(e){
        e.preventDefault();
        var form = $(this);
        var url = form.attr('action');

        var amount = debitAmountMask.unmaskedValue;
        var comment = form.find('textarea[name="comment"]').val();

        if (!amount || parseFloat(amount) <= 0) {
            alert('El monto debe ser un número positivo.');
            return;
        }
        if (!comment) {
            alert('El comentario no puede estar vacío.');
            return;
        }
        
        var formData = new FormData();
        
        formData.append('amount', amount);
        formData.append('comment', comment);

        for (let i = 0; i < debitFiles.files.length; i++) {
            formData.append('photos[]', debitFiles.files[i]);
        }

        $.ajax({
            url: url,
            type: 'post',
            data: formData,
            contentType: false,
            processData: false,
            success: function(data){
                if(data.success){
                    $('#debit-modal').modal('hide');
                    $.pjax.reload({container:'#transactions-pjax'});
                    updateWalletBalance();
                    // Clear form
                    $('#debit-form')[0].reset();
                    $('#debit-image-preview').empty();
                    debitFiles.items.clear();
                } else {
                    alert(data.message);
                }
            },
            error: function(jqXHR){
                alert(jqXHR.responseJSON.message);
            }
        });
    });
});
JS;
$this->registerJs($script);
?> 