<?php

namespace common\services;

use common\models\telefono\Telefono;

class GananciaService
{
    /**
     * Calcula la ganancia neta, del socio y de la empresa para un teléfono.
     * @param Telefono $telefono
     * @return GananciaDto
     */
    public function calcular(Telefono $telefono): GananciaDto
    {
        $dto = new GananciaDto();

        $dto->neta = $telefono->precio_venta_recomendado - $telefono->getCostoTotal();

        if ($telefono->socio_id) {
            $dto->porcentajeSocio = $telefono->socio_porcentaje ?: 20.00;
            $dto->socio = $dto->neta * ($dto->porcentajeSocio / 100);
        }

        $dto->empresa = $dto->neta - $dto->socio;
        $dto->porcentajeEmpresa = 100.0 - $dto->porcentajeSocio;

        // Evitar resultados negativos en la ganancia si hay pérdidas
        if ($dto->neta < 0) {
            $dto->socio = 0;
            $dto->empresa = $dto->neta; // La empresa asume la pérdida
        }

        return $dto;
    }
} 