<?php

namespace common\usecases\telefono\gasto;

use common\models\telefono\TelefonoGasto;

class UpdateTelefonoGastoUseCase
{
    /**
     * @param int $gastoId
     * @param string $descripcion
     * @param float $montoGasto
     * @return TelefonoGasto|null
     */
    public function execute(int $gastoId, string $descripcion, float $montoGasto): ?TelefonoGasto
    {
        $gasto = TelefonoGasto::findOne($gastoId);
        if (!$gasto) {
            return null;
        }

        $gasto->descripcion = $descripcion;
        $gasto->monto_gasto = $montoGasto;

        if ($gasto->save()) {
            return $gasto;
        }

        return null;
    }
} 