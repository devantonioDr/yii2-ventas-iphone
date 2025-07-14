<?php

namespace common\services\telefono;

use common\models\telefono\Telefono;

class GananciaService
{
    /**
     * Calcula la ganancia neta, del socio y de la empresa para un teléfono.
     * @param Telefono $telefono
     * @return array{neta: float, socio: float, empresa: float, porcentajeSocio: float, porcentajeEmpresa: float}
     */
    public static function calcular(Telefono $telefono): array
    {
        $ganancia = [
            'neta' => 0.0,
            'socio' => 0.0,
            'empresa' => 0.0,
            'porcentajeSocio' => 0.0,
            'porcentajeEmpresa' => 100.0,
        ];

        $ganancia['neta'] = $telefono->precio_venta_recomendado - $telefono->getCostoTotal();

        if ($telefono->socio_id) {
            $ganancia['porcentajeSocio'] = $telefono->socio_porcentaje ?: 20.00;
            $ganancia['socio'] = $ganancia['neta'] * ($ganancia['porcentajeSocio'] / 100);
        }

        $ganancia['empresa'] = $ganancia['neta'] - $ganancia['socio'];
        $ganancia['porcentajeEmpresa'] = 100.0 - $ganancia['porcentajeSocio'];

        // Evitar resultados negativos en la ganancia si hay pérdidas
        if ($ganancia['neta'] < 0) {
            $ganancia['socio'] = 0;
            $ganancia['empresa'] = $ganancia['neta']; // La empresa asume la pérdida
        }

        return $ganancia;
    }

    /**
     * @param $socioId
     * @return array{neta: float, socio: float, empresa: float, precioAdquisicion: float, gastos: float}
     */
    public static function calcularTotalGanaciaPendienteSocio($socioId): array
    {
        $total = [
            'neta' => 0.0,
            'socio' => 0.0,
            'empresa' => 0.0,
            'precioAdquisicion' => 0.0,
            'gastos' => 0.0,
        ];

        $telefonos = Telefono::find()
            ->where(['socio_id' => $socioId, 'status' => Telefono::STATUS_VENDIDO])
            ->all();

        foreach ($telefonos as $telefono) {
            $ganancia = self::calcular($telefono);
            
            $total['neta'] += $ganancia['neta'];
            $total['socio'] += $ganancia['socio'];
            $total['empresa'] += $ganancia['empresa'];
            $total['precioAdquisicion'] += $telefono->precio_adquisicion;
            $total['gastos'] += $telefono->getTotalGastos();
        }
        
        return $total;
    }
}
