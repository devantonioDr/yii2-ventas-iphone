<?php

namespace common\services\compra;

use common\models\telefono\TelefonoCompra;
use common\services\telefono\GananciaService;

class CompraService
{
    public static function getSummary(TelefonoCompra $compra)
    {
        $summary = [
            'invertido' => 0,
            'gasto_total' => 0,
            'ganancia_neta_total' => 0,
            'ganancia_empresa_total' => 0,
            'ganancia_socio_total' => 0,
        ];

        foreach ($compra->telefonos as $telefono) {
            $ganancia = GananciaService::calcular($telefono);
            $summary['invertido'] += $telefono->precio_adquisicion;
            $summary['gasto_total'] += $telefono->getTotalGastos();
            $summary['ganancia_neta_total'] += $ganancia['neta'];
            $summary['ganancia_empresa_total'] += $ganancia['empresa'];
            $summary['ganancia_socio_total'] += $ganancia['socio'];
        }

        return $summary;
    }
} 