<?php
/* @var $telefono common\models\telefono\Telefono */
/* @var $ganancia array */
/* @var $costo_total float */
use yii\helpers\Html;
?>
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <h4 class="modal-title" id="sellModalLabel">Registrar venta</h4>
  <p class="text-muted" style="margin:8px 0 0">
    <?= Html::encode($telefono->marca) ?> | <?= Html::encode($telefono->modelo) ?> | IMEI: <?= Html::encode($telefono->imei) ?>
    <?php if ($telefono->socio): ?> | Socio: <?= Html::encode($telefono->socio->nombre) ?><?php endif; ?>
  </p>
  <hr style="margin:8px 0 0"/>
  </div>
<div class="modal-body">
  <div id="sell-default" style="margin-bottom:8px">
    <div class="row">
      <div class="col-xs-6"><small class="text-muted">Costo total (actual)</small><div id="sell-def-costo-total" class="text-bold">RD$ <?= number_format($costo_total, 2) ?></div></div>
      <div class="col-xs-6"><small class="text-muted">Ganancia neta (actual)</small><div id="sell-def-ganancia-neta" class="text-bold">RD$ <?= number_format($ganancia['neta'] ?? 0, 2) ?></div></div>
    </div>
    <div class="row" style="margin-top:6px">
      <div class="col-xs-6"><small class="text-muted">Gan. socio (actual)</small><div id="sell-def-ganancia-socio" class="text-bold">RD$ <?= number_format($ganancia['socio'] ?? 0, 2) ?></div></div>
      <div class="col-xs-6"><small class="text-muted">Gan. empresa (actual)</small><div id="sell-def-ganancia-empresa" class="text-bold">RD$ <?= number_format($ganancia['empresa'] ?? 0, 2) ?></div></div>
    </div>
    <hr style="margin:8px 0"/>
  </div>

  <div class="form-group">
    <label for="sell-precio-input">Precio de venta (RD$)</label>
    <input type="text" class="form-control" id="sell-precio-input" placeholder="0.00">
    <small id="sell-error" class="text-danger" style="display:none"></small>
  </div>

  <div id="sell-preview" style="display:none">
    <div class="row">
      <div class="col-xs-6"><small class="text-muted">Costo total (nuevo)</small><div id="sell-costo-total" class="text-bold">RD$ 0.00</div></div>
      <div class="col-xs-6"><small class="text-muted">Ganancia neta (nueva)</small><div id="sell-ganancia-neta" class="text-bold">RD$ 0.00</div></div>
    </div>
    <div class="row" style="margin-top:6px">
      <div class="col-xs-6"><small class="text-muted">Gan. socio (nueva)</small><div id="sell-ganancia-socio" class="text-bold">RD$ 0.00</div></div>
      <div class="col-xs-6"><small class="text-muted">Gan. empresa (nueva)</small><div id="sell-ganancia-empresa" class="text-bold">RD$ 0.00</div></div>
    </div>
  </div>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
  <button type="button" class="btn btn-primary" id="sell-submit-btn">Vender</button>
</div>

