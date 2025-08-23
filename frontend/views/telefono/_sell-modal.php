<?php
/* @var $this yii\web\View */
?>
<!-- Modal para capturar precio de venta -->
<div class="modal fade" id="sellModal" tabindex="-1" role="dialog" aria-labelledby="sellModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div id="sell-modal-content"><!-- se llenará por AJAX --></div>
    </div>
  </div>
</div>
<?php
// Nota: este script asume que imask.js ya está registrado en la vista principal.
$this->registerJs(<<<'JS'
var SELL_MODAL_URL = null;
var SELL_PREVIEW_URL = null;
var sellPriceMask = null;
var SELL_DEFAULT = null;
var sellMaskOptions = {
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
(function initSellMask(){
    var el = document.getElementById('sell-precio-input');
    if (el && window.IMask) {
        sellPriceMask = IMask(el, sellMaskOptions);
    }
})();

function sellFormatMoney(n){
    n = parseFloat(n || 0);
    return 'RD$ ' + n.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

function sellSetColor($el, isUp){
    $el.removeClass('text-success text-danger');
    if (isUp === true) $el.addClass('text-success');
    if (isUp === false) $el.addClass('text-danger');
}

function sellRenderDefault(data){
    if (!data || !data.success) { $('#sell-default').hide(); return; }
    $('#sell-def-costo-total').text(sellFormatMoney(data.costo_total || 0));
    var g = data.ganancia || {};
    $('#sell-def-ganancia-neta').text(sellFormatMoney(g.neta || 0));
    $('#sell-def-ganancia-socio').text(sellFormatMoney(g.socio || 0));
    $('#sell-def-ganancia-empresa').text(sellFormatMoney(g.empresa || 0));
    $('#sell-default').show();
}

function sellRenderPreview(data){
    if (!data || !data.success) { $('#sell-preview').hide(); return; }
    var base = SELL_DEFAULT || {};
    var baseG = (base.ganancia || {});
    $('#sell-costo-total').text(sellFormatMoney(data.costo_total || 0));
    var g = data.ganancia || {};
    $('#sell-ganancia-neta').text(sellFormatMoney(g.neta || 0));
    $('#sell-ganancia-socio').text(sellFormatMoney(g.socio || 0));
    $('#sell-ganancia-empresa').text(sellFormatMoney(g.empresa || 0));
    sellSetColor($('#sell-ganancia-neta'), (g.neta || 0) > (baseG.neta || 0));
    sellSetColor($('#sell-ganancia-socio'), (g.socio || 0) > (baseG.socio || 0));
    sellSetColor($('#sell-ganancia-empresa'), (g.empresa || 0) > (baseG.empresa || 0));
    $('#sell-preview').show();
}

function sellRequestPreview(setAsDefault){
    if (!SELL_PREVIEW_URL) return;
    var precio = null;
    if (sellPriceMask){
        precio = parseFloat(sellPriceMask.unmaskedValue || '0');
    } else {
        var raw = ($('#sell-precio-input').val() || '').toString().trim();
        raw = raw.replace(/,/g, '').replace(',', '.');
        precio = parseFloat(raw);
    }
    if (!precio || precio <= 0) { if (!setAsDefault) $('#sell-preview').hide(); return; }
    $.post(SELL_PREVIEW_URL, { precio_venta: precio })
      .done(function(resp){
          if (setAsDefault) {
              SELL_DEFAULT = resp;
              sellRenderDefault(resp);
          }
          sellRenderPreview(resp);
      })
      .fail(function(){ if (!setAsDefault) $('#sell-preview').hide(); });
}

$(document).on('click', '.js-mark-vendido', function(e){
    e.preventDefault();
    var btn = $(this);
    var url = btn.data('url');
    var previewUrl = btn.data('preview-url');
    var precio = parseFloat(btn.data('precio'));
    if(!url) return;
    SELL_MODAL_URL = url;
    SELL_PREVIEW_URL = previewUrl || null;
    SELL_DEFAULT = null;
    // Cargar el contenido del modal vía AJAX
    var loadUrl = btn.data('modal-url');
    if (!loadUrl) {
        // fallback: mostrar modal vacío
        $('#sell-modal-content').html('<div class="modal-header">\
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>\
<h4 class="modal-title" id="sellModalLabel">Registrar venta</h4></div>\
<div class="modal-body"><p>No se pudo cargar el contenido.</p></div>');
        $('#sellModal').modal('show');
        return;
    }
    $('#sell-modal-content').html('<div class="modal-body"><div class="text-center" style="padding:20px"><i class="fa fa-spinner fa-spin"></i> Cargando...</div></div>');
    $('#sellModal').modal('show');
    $.get(loadUrl)
      .done(function(html){
          $('#sell-modal-content').html(html);
          // Inicializar máscara si el input existe
          var el = document.getElementById('sell-precio-input');
          if (el && window.IMask) { sellPriceMask = IMask(el, sellMaskOptions); }
          if(isFinite(precio) && precio > 0){
              if(sellPriceMask){ sellPriceMask.value = precio.toFixed(2); }
              else { $('#sell-precio-input').val(precio.toFixed(2)); }
          }
          sellRequestPreview(true);
      })
      .fail(function(){
          $('#sell-modal-content').html('<div class="modal-body"><div class="alert alert-danger">No se pudo cargar el modal.</div></div>');
      });
});

// Vista previa reactiva al escribir/cambiar el precio (no cambia los valores por defecto)
$(document).on('keyup change', '#sell-precio-input', function(){
    sellRequestPreview(false);
});

$(document).on('click', '#sell-submit-btn', function(){
    if(!SELL_MODAL_URL) return;
    var precio = null;
    if(sellPriceMask){
        var unmasked = sellPriceMask.unmaskedValue;
        precio = parseFloat(unmasked);
    } else {
        var raw = ($('#sell-precio-input').val() || '').toString().trim();
        raw = raw.replace(/,/g, '').replace(',', '.');
        precio = parseFloat(raw);
    }
    if(!precio || precio <= 0) {
        $('#sell-error').text('Ingrese un precio válido (> 0).').show();
        return;
    }
    // Regla de seguridad en frontend: no permitir por debajo del costo actual (si disponible)
    var base = SELL_DEFAULT || {};
    var costo = parseFloat(base.costo_total || 0);
    if (costo && precio < costo) {
        $('#sell-error').text('El precio de venta no puede ser menor al costo total.').show();
        return;
    }
    var btn = $(this);
    btn.prop('disabled', true);
    $.post(SELL_MODAL_URL, { precio_venta: precio })
        .done(function(resp){
            if(resp && resp.success){
                $('#sellModal').modal('hide');
                if (window.Swal) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Venta registrada',
                        text: 'El teléfono fue marcado como vendido.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
                $.pjax.reload({container:'#telefono-grid-pjax'});
            } else {
                var msg = (resp && resp.message) ? resp.message : 'No se pudo registrar la venta';
                if (window.Swal) {
                    Swal.fire({ icon: 'error', title: 'Error', text: msg });
                } else {
                    $('#sell-error').text(msg).show();
                }
            }
        })
        .fail(function(){
            if (window.Swal) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Error en la solicitud' });
            } else {
                $('#sell-error').text('Error en la solicitud').show();
            }
        })
        .always(function(){
            btn.prop('disabled', false);
        });
});
JS);
?>
