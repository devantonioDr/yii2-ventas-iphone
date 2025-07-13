<?php

namespace common\usecases\telefono\gasto;

use common\models\telefono\Telefono;
use common\models\telefono\TelefonoGasto;
use Yii;

class CreateTelefonoGastoUseCase
{
    /**
     * @param int $telefonoId
     * @param string $descripcion
     * @param float $montoGasto
     * @return TelefonoGasto|null
     */
    public function execute(int $telefonoId, string $descripcion, float $montoGasto): ?TelefonoGasto
    {
        $telefono = Telefono::findOne($telefonoId);
        if (!$telefono) {
            return null;
        }

        $gasto = new TelefonoGasto();
        $gasto->telefono_id = $telefonoId;
        $gasto->descripcion = $descripcion;
        $gasto->monto_gasto = $montoGasto;

        if ($gasto->save()) {
            return $gasto;
        }

        return null;
    }
} 