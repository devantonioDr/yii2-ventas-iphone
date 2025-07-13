<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $telefono common\models\telefono\Telefono */
?>

<div class="modal fade" id="desgloseModal" tabindex="-1" role="dialog" aria-labelledby="desgloseModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="desgloseModalLabel">
                    <i class="fa fa-calculator"></i> Desglose de Ganancia
                </h4>
            </div>
            <div class="modal-body" id="desgloseContent">
                <!-- El contenido se cargará aquí -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="imprimirDesglose()">
                    <i class="fa fa-print"></i> Imprimir
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Objeto para cachear el contenido del desglose
var desgloseCache = {};

function cargarDesglose(telefonoId) {
    var modalContent = $('#desgloseContent');

    // 1. Revisar si el contenido ya está en caché
    if (desgloseCache[telefonoId]) {
        modalContent.html(desgloseCache[telefonoId]);
        $('#desgloseModal').modal('show');
        return; // Salir de la función para evitar la llamada AJAX
    }

    // Si no está en caché, mostrar el loader y hacer la petición
    var loadingHtml = `
        <div class="text-center" style="padding: 40px 0;">
            <i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>
            <p style="margin-top: 10px;">Cargando desglose...</p>
        </div>`;

    modalContent.html(loadingHtml);
    $('#desgloseModal').modal('show');
    
    setTimeout(function() {
        $.ajax({
            url: '<?= Url::to(['telefono/get-desglose']) ?>',
            type: 'GET',
            data: { id: telefonoId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // 2. Guardar el resultado en caché antes de mostrarlo
                    desgloseCache[telefonoId] = response.html;
                    modalContent.html(response.html);
                } else {
                    modalContent.html('<div class="alert alert-danger">Error al cargar el desglose.</div>');
                }
            },
            error: function() {
                modalContent.html('<div class="alert alert-danger">Error de conexión. Por favor, intente de nuevo.</div>');
            }
        });
    }, 2000);
}

function imprimirDesglose() {
    var contenido = $('#desgloseContent').html();
    var ventana = window.open('', '_blank');
    ventana.document.write(`
        <html>
        <head>
            <title>Desglose de Ganancia</title>
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .desglose-factura { border: 1px solid #ccc; padding: 20px; max-width: 800px; margin: auto; }
                .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
                .seccion { margin-bottom: 20px; }
                .seccion h4 { color: #333; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
                .item { display: flex; justify-content: space-between; margin: 5px 0; }
                .total { font-weight: bold; border-top: 2px solid #333; padding-top: 10px; margin-top: 10px; display: flex; justify-content: space-between;}
                .text-right { text-align: right; }
                .text-center { text-align: center; }
                .text-warning { color: #f0ad4e; }
                .text-success { color: #5cb85c; }
            </style>
        </head>
        <body>
            <div class="desglose-factura">
                ${contenido}
            </div>
        </body>
        </html>
    `);
    ventana.document.close();
    ventana.focus();
    setTimeout(function () {
        ventana.print();
        ventana.close();
    }, 250);
}
</script> 