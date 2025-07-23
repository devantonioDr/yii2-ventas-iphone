<?php

namespace common\usecases\telefono_gasto;

use common\models\telefono\TelefonoGasto;
use yii\base\Exception;

class DeleteTelefonoGastoUseCase
{
    public function execute(int $gastoId): void
    {
        $gasto = TelefonoGasto::findOne($gastoId);
        if ($gasto === null) {
            throw new \RuntimeException('Gasto no encontrado.');
        }

        if ($gasto->delete() === false) {
            throw new Exception('No se pudo eliminar el gasto.');
        }
    }
} 