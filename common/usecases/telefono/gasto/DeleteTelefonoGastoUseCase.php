<?php

namespace common\usecases\telefono\gasto;

use common\models\telefono\TelefonoGasto;

class DeleteTelefonoGastoUseCase
{
    /**
     * @param int $gastoId
     * @return bool
     */
    public function execute(int $gastoId): bool
    {
        $gasto = TelefonoGasto::findOne($gastoId);
        if (!$gasto) {
            return false;
        }

        return $gasto->delete() !== false;
    }
} 