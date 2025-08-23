<?php

namespace common\usecases\telefono;

use common\models\telefono\Telefono;

class MarkTelefonoAsVendidoUseCase
{
    /**
     * Marca un teléfono como vendido si está en tienda y guarda el precio real de venta.
     *
     * @param int $telefonoId
     * @param float $precioVentaReal
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function execute(int $telefonoId, float $precioVentaReal): bool
    {
        $telefono = Telefono::findOne($telefonoId);
        if (!$telefono) {
            throw new \InvalidArgumentException('Teléfono no encontrado.');
        }

        // Solo permitir transición desde EN_TIENDA -> VENDIDO
        if ($telefono->status !== Telefono::STATUS_EN_TIENDA) {
            throw new \InvalidArgumentException('Solo se puede vender un teléfono en estado "En tienda".');
        }

        if ($precioVentaReal <= 0) {
            throw new \InvalidArgumentException('El precio de venta debe ser mayor a 0.');
        }

        // No permitir vender por debajo del costo total (adquisición + gastos)
        $costoTotal = (float)$telefono->getCostoTotal();
        if ($precioVentaReal < $costoTotal) {
            $costoFmt = number_format($costoTotal, 2);
            throw new \InvalidArgumentException('El precio de venta no puede ser menor al costo total (RD$ ' . $costoFmt . ').');
        }

        $telefono->status = Telefono::STATUS_VENDIDO;
        // Guardamos el precio de venta real en el mismo campo recomendado si no hay otro campo
        $telefono->precio_venta_recomendado = $precioVentaReal;
        if ($telefono->save(false, ['status', 'precio_venta_recomendado'])) {
            return true;
        }

        return false;
    }
}


